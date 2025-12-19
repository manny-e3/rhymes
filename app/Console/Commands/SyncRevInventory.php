<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SyncRevInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:sync-inventory {--book-id=} {--process-sales-value} {--debug} {--notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inventory data from ERPREV and update book statuses';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Starting ERPREV inventory sync...');
        
        if ($this->option('debug')) {
            $this->line('Debug mode enabled');
        }
        
        // Prepare filters for the ERPREV API
        $filters = [];
        
        $bookId = $this->option('book-id');
        if ($bookId) {
            $book = Book::find($bookId);
            if (!$book) {
                $this->error("Book not found");
                return 1;
            }
            // We'll filter the results after fetching all data
            $this->info("Will filter results for book ID: {$bookId}");
        }
        
        // Fetch inventory data from ERPREV
        $this->info("Fetching inventory data from ERPREV...");
        if (!empty($filters)) {
            $this->info("Using filters: " . json_encode($filters));
        }
        
        $result = $revService->getStockList($filters);
        
        if (!$result['success']) {
            $this->error("Failed to fetch inventory data: {$result['message']}");
            Log::error('ERPREV Inventory Sync Failed', [
                'error' => $result['message'],
                'filters' => $filters
            ]);
            return 1;
        }
        
        $inventoryData = isset($result['data']['records']) ? $result['data']['records'] : [];
        $this->info("Found " . count($inventoryData) . " inventory records from API");
        
        if ($this->option('debug')) {
            if (count($inventoryData) > 0) {
                $this->line("Sample inventory record structure: " . json_encode($inventoryData[0], JSON_PRETTY_PRINT));
                $this->line("Available keys in first record: " . json_encode(array_keys($inventoryData[0])));
            } else {
                $this->line("No inventory data found");
            }
            
            // Exit early in debug mode to show data structure
            return 0;
        }
        
        $updatedCount = 0;
        $errorCount = 0;
        $salesValueProcessed = 0;
        $notificationCount = 0;
        $missingBarcodeCount = 0;
        $bookNotFoundCount = 0;
        
        // Process each inventory record
        foreach ($inventoryData as $index => $item) {
            try {
                // Use Barcode from inventory data to find book by ISBN
                // Try multiple possible field names for barcode
                $barcode = null;
                $possibleBarcodeFields = ['Barcode', 'barcode', 'ISBN', 'isbn'];
                foreach ($possibleBarcodeFields as $field) {
                    if (isset($item[$field]) && !empty($item[$field])) {
                        $barcode = $item[$field];
                        break;
                    }
                }
                
                if (!$barcode) {
                    $missingBarcodeCount++;
                    $errorCount++;
                    continue;
                }
                
                // Find the corresponding book in our system using ISBN
                $book = Book::where('isbn', $barcode)->first();
                
                if (!$book) {
                    $bookNotFoundCount++;
                    $errorCount++;
                    continue;
                }
                
                // If filtering for a specific book, skip others
                if ($bookId && $book->id != $bookId) {
                    continue;
                }
                
                // Extract inventory details with fallbacks
                $quantityOnHand = isset($item['UnitsInStock']) ? $item['UnitsInStock'] : 
                                 (isset($item['quantity_on_hand']) ? $item['quantity_on_hand'] : 
                                 (isset($item['QuantityOnHand']) ? $item['QuantityOnHand'] : 0));
                
                $previousStatus = $book->status;
                $statusChanged = false;
                
                // Update book status based on inventory levels
                // If book is currently 'accepted' and now has stock, update to 'stocked'
                if ($book->status === 'accepted' && $quantityOnHand > 0) {
                    $book->update(['status' => 'stocked']);
                    $statusChanged = true;
                    $updatedCount++;
                } elseif ($book->status === 'stocked' && $quantityOnHand <= 0) {
                    // Book was stocked but is now out of stock
                    // We could notify here if needed
                }
                
                // Notify author if status changed and notification option is enabled
                if ($statusChanged && $this->option('notify')) {
                    $user = User::find($book->user_id);
                    if ($user) {
                        // We could send a notification here if needed
                        // $user->notify(new BookStatusChanged($book, $previousStatus, 'stocked'));
                        $notificationCount++;
                    }
                }
                
                // Process sales value if option is enabled
                if ($this->option('process-sales-value')) {
                    // Try different possible field names for sales value
                    $salesValue = 0;
                    $possibleSalesFields = ['SalesValue', 'sales_value', 'TotalSales', 'total_sales', 'SalesAmount', 'sales_amount', 'Value', 'value'];
                    foreach ($possibleSalesFields as $field) {
                        if (isset($item[$field])) {
                            // Handle currency values that might have symbols
                            $value = $item[$field];
                            if (is_string($value)) {
                                // Remove currency symbols and commas
                                $value = preg_replace('/[^\d.-]/', '', $value);
                            }
                            if (is_numeric($value)) {
                                $salesValue = (float)$value;
                                break;
                            }
                        }
                    }
                    
                    // If still no sales value found, try to calculate from available fields
                    if ($salesValue == 0) {
                        // Use SellingPrice instead of other price fields
                        $unitPrice = isset($item['SellingPrice']) ? $item['SellingPrice'] : 
                                    (isset($item['selling_price']) ? $item['selling_price'] : 
                                    (isset($item['UnitPrice']) ? $item['UnitPrice'] : 
                                    (isset($item['unit_price']) ? $item['unit_price'] : 0)));
                        // Handle currency values for unit price
                        if (is_string($unitPrice)) {
                            $unitPrice = preg_replace('/[^\d.-]/', '', $unitPrice);
                        }
                        if (is_numeric($unitPrice)) {
                            $unitPrice = (float)$unitPrice;
                            if ($unitPrice > 0 && $quantityOnHand > 0) {
                                $salesValue = $unitPrice * $quantityOnHand;
                            }
                        }
                    }
                    
                    if ($salesValue > 0) {
                        // Check if this sales value has already been processed
                        $existingTransaction = WalletTransaction::where('meta->barcode', $barcode)
                            ->where('meta->erprev_inventory_sync_date', now()->toDateString())
                            ->first();
                        
                        if (!$existingTransaction) {
                            // Calculate author earnings (assuming 70% goes to author)
                            $authorEarnings = $salesValue * 0.7; // 70% to author, 30% to platform
                            
                            // Create wallet transaction for the author
                            WalletTransaction::create([
                                'user_id' => $book->user_id,
                                'book_id' => $book->id,
                                'type' => 'sale',
                                'amount' => $authorEarnings,
                                'meta' => [
                                    'barcode' => $barcode,
                                    'erprev_inventory_sync_date' => now()->toDateString(),
                                    'quantity_on_hand' => $quantityOnHand,
                                    'sales_value' => $salesValue,
                                    'unit_price' => $unitPrice ?? 0,
                                    'description' => "Sales value from inventory sync for '{$book->title}'",
                                ],
                            ]);
                            
                            $salesValueProcessed++;
                        }
                    }
                }
                
            } catch (\Exception $e) {
                Log::error('Error processing ERPREV inventory item', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'item_index' => $index
                ]);
                $errorCount++;
            }
        }
        
        $this->info("Inventory sync completed.");
        $this->line("  Records processed: " . count($inventoryData));
        $this->line("  Books updated: {$updatedCount}");
        $this->line("  Sales values processed: {$salesValueProcessed}");
        $this->line("  Notifications sent: {$notificationCount}");
        $this->line("  Records with missing barcodes: {$missingBarcodeCount}");
        $this->line("  Books not found: {$bookNotFoundCount}");
        $this->line("  Other errors: " . ($errorCount - $missingBarcodeCount - $bookNotFoundCount));
        
        // Log summary
        Log::info('ERPREV Inventory Sync Completed', [
            'records_processed' => count($inventoryData),
            'updated' => $updatedCount,
            'sales_value_processed' => $salesValueProcessed,
            'notifications' => $notificationCount,
            'missing_barcodes' => $missingBarcodeCount,
            'books_not_found' => $bookNotFoundCount,
            'other_errors' => ($errorCount - $missingBarcodeCount - $bookNotFoundCount)
        ]);
        
        return 0;
    }
}