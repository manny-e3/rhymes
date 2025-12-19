<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\RevService;
use App\Models\Book;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Notifications\BookStatusChanged;

class SyncRevInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [1, 5, 10];
    
    protected $bookId;
    protected $processSalesValue;
    protected $notify;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bookId = null, $processSalesValue = false, $notify = false)
    {
        $this->bookId = $bookId;
        $this->processSalesValue = $processSalesValue;
        $this->notify = $notify;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RevService $revService)
    {
        Log::info('Starting ERPREV Inventory Sync Job', [
            'book_id' => $this->bookId,
            'process_sales_value' => $this->processSalesValue,
            'notify' => $this->notify
        ]);
        
        try {
            // Prepare filters for the ERPREV API
            $filters = [];
            
            if ($this->bookId) {
                $book = Book::find($this->bookId);
                if (!$book || !$book->rev_book_id) {
                    Log::error('Book not found or not registered in ERPREV', ['book_id' => $this->bookId]);
                    return;
                }
                $filters['product_id'] = $book->rev_book_id;
            }
            
            // Fetch inventory data from ERPREV
            Log::info('Fetching inventory data from ERPREV', ['filters' => $filters]);
            $result = $revService->getStockList($filters);
            
            if (!$result['success']) {
                Log::error('Failed to fetch inventory data', ['error' => $result['message']]);
                return;
            }
            
            $inventoryData = isset($result['data']['records']) ? $result['data']['records'] : [];
            Log::info('Found inventory records to process', ['count' => count($inventoryData)]);
            
            $updatedCount = 0;
            $errorCount = 0;
            $salesValueProcessed = 0;
            $notificationCount = 0;
            
            // Process each inventory record
            foreach ($inventoryData as $item) {
                try {
                    // Find the corresponding book in our system
                    $productId = isset($item['ProductID']) ? $item['ProductID'] : (isset($item['product_id']) ? $item['product_id'] : null);
                    $book = Book::where('rev_book_id', $productId)->first();
                    
                    if (!$book) {
                        $displayProductId = $productId ?: 'N/A';
                        Log::warning('Book with ERPREV product ID not found in system', ['product_id' => $displayProductId]);
                        $errorCount++;
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
                        Log::info('Updated book status to stocked', [
                            'book_id' => $book->id,
                            'title' => $book->title,
                            'quantity_on_hand' => $quantityOnHand
                        ]);
                        $updatedCount++;
                    } elseif ($book->status === 'stocked' && $quantityOnHand <= 0) {
                        // Book was stocked but is now out of stock
                        Log::info('Book is out of stock', [
                            'book_id' => $book->id,
                            'title' => $book->title,
                            'quantity_on_hand' => $quantityOnHand
                        ]);
                    }
                    
                    // Notify author if status changed and notification option is enabled
                    if ($statusChanged && $this->notify) {
                        $user = User::find($book->user_id);
                        if ($user) {
                            // Send notification to author about status change
                            $user->notify(new BookStatusChanged($book, $previousStatus, 'stocked'));
                            $notificationCount++;
                            Log::info('Sent status change notification to author', [
                                'user_id' => $user->id,
                                'book_id' => $book->id,
                                'previous_status' => $previousStatus,
                                'new_status' => 'stocked'
                            ]);
                        }
                    }
                    
                    // Process sales value if option is enabled
                    if ($this->processSalesValue) {
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
                            $unitPrice = isset($item['SellingPrice']) ? $item['SellingPrice'] : 
                                        (isset($item['selling_price']) ? $item['selling_price'] : 
                                        (isset($item['UnitPrice']) ? $item['UnitPrice'] : 0));
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
                            $existingTransaction = WalletTransaction::where('meta->erprev_product_id', $productId)
                                ->where('meta->erprev_inventory_sync_date', now()->toDateString())
                                ->first();
                            
                            if (!$existingTransaction) {
                                // Calculate author earnings (70% goes to author, 30% to platform)
                                $authorEarnings = $salesValue * 0.7;
                                $platformFee = $salesValue * 0.3;
                                
                                // Create wallet transaction for the author
                                WalletTransaction::create([
                                    'user_id' => $book->user_id,
                                    'book_id' => $book->id,
                                    'type' => 'sale',
                                    'amount' => $authorEarnings,
                                    'meta' => [
                                        'erprev_product_id' => $productId,
                                        'erprev_inventory_sync_date' => now()->toDateString(),
                                        'quantity_on_hand' => $quantityOnHand,
                                        'sales_value' => $salesValue,
                                        'platform_fee' => $platformFee,
                                        'author_earnings' => $authorEarnings,
                                        'description' => "Sales value from inventory sync for '{$book->title}'",
                                    ],
                                ]);
                                
                                // Also create a transaction for the platform fee
                                WalletTransaction::create([
                                    'user_id' => $book->user_id, // Platform user ID - you might want to change this
                                    'book_id' => $book->id,
                                    'type' => 'platform_fee',
                                    'amount' => -$platformFee, // Negative because it's a deduction
                                    'meta' => [
                                        'erprev_product_id' => $productId,
                                        'author_id' => $book->user_id,
                                        'quantity_on_hand' => $quantityOnHand,
                                        'sales_value' => $salesValue,
                                        'platform_fee' => $platformFee,
                                        'erprev_inventory_sync_date' => now()->toDateString(),
                                        'description' => "Platform fee for inventory sync of '{$book->title}'",
                                    ],
                                ]);
                                
                                $salesValueProcessed++;
                                Log::info('Processed sales value from inventory', [
                                    'book_id' => $book->id,
                                    'title' => $book->title,
                                    'sales_value' => $salesValue,
                                    'author_earnings' => $authorEarnings,
                                    'platform_fee' => $platformFee
                                ]);
                            } else {
                                Log::debug('Sales value already processed today, skipping', [
                                    'book_id' => $book->id,
                                    'product_id' => $productId
                                ]);
                            }
                        }
                    }
                    
                } catch (\Exception $e) {
                    $displayProductId = isset($item['ProductID']) ? $item['ProductID'] : (isset($item['product_id']) ? $item['product_id'] : 'N/A');
                    Log::error('Error processing ERPREV inventory item', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'product_id' => $displayProductId
                    ]);
                    $errorCount++;
                }
            }
            
            Log::info('ERPREV Inventory Sync Job Completed', [
                'updated' => $updatedCount,
                'sales_value_processed' => $salesValueProcessed,
                'notifications' => $notificationCount,
                'errors' => $errorCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('ERPREV Inventory Sync Job Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to allow retries
        }
    }
}