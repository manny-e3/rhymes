<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;
use App\Models\WalletTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncRevSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:sync-sales {--since=} {--book-id=} {--days=1} {--debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync sales data from ERPREV and update author wallets';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Starting ERPREV sales sync...');
        
        if ($this->option('debug')) {
            $this->line('Debug mode enabled');
        }
        
        // Determine the date range for syncing
        $since = $this->option('since') ? Carbon::parse($this->option('since')) : Carbon::now()->subDays($this->option('days'));
        $bookId = $this->option('book-id');
        
        // Prepare filters for the ERPREV API
        $filters = [
            'date_from' => $since->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
        ];
        
        // Fetch sales data from ERPREV
        $this->info("Fetching sales data from ERPREV since {$filters['date_from']}...");
        $result = $revService->getSalesItems($filters);
        
        if (!$result['success']) {
            $this->error("Failed to fetch sales data: {$result['message']}");
            Log::error('ERPREV Sales Sync Failed', [
                'error' => $result['message'],
                'filters' => $filters
            ]);
            return 1;
        }
        
        $salesData = $result['data']['data'] ?? $result['data']['records'] ?? [];
        $this->info("Found " . count($salesData) . " sales records to process");
        
        if ($this->option('debug')) {
            if (count($salesData) > 0) {
                $this->line("Sample sales data structure: " . json_encode($salesData[0], JSON_PRETTY_PRINT));
                $this->line("Available keys in first record: " . json_encode(array_keys($salesData[0])));
                // Show a few more records to check for date fields
                for ($i = 0; $i < min(3, count($salesData)); $i++) {
                    $this->line("Record {$i}: " . json_encode($salesData[$i]));
                }
            } else {
                $this->line("No sales data found");
            }
            
            // Exit early in debug mode to show data structure
            return 0;
        }
        
        $processedCount = 0;
        $errorCount = 0;
        $duplicateCount = 0;
        $bookNotFoundCount = 0;
        $bookNotAcceptedCount = 0;
        
        // Process each sale record
        foreach ($salesData as $sale) {
            try {
                // Use Barcode from sales data to find book by ISBN
                $barcode = $sale['Barcode'] ?? $sale['barcode'] ?? null;
                $productId = $sale['ProductID'] ?? $sale['product_id'] ?? null;
                
                if (!$barcode && !$productId) {
                    $errorCount++;
                    continue;
                }
                
                // Find the corresponding book in our system using ISBN or ProductID
                $book = null;
                if ($barcode) {
                    $book = Book::where('isbn', $barcode)->first();
                }
                
                if (!$book && $productId) {
                    $book = Book::where('rev_book_id', $productId)->first();
                }
                
                if (!$book) {
                    $bookNotFoundCount++;
                    $errorCount++;
                    continue;
                }
                
                // Check if book has been accepted
                if ($book->status !== 'accepted' && $book->status !== 'stocked') {
                    $bookNotAcceptedCount++;
                    $errorCount++;
                    continue;
                }
                
                // If filtering for a specific book, skip others
                if ($bookId && $book->id != $bookId) {
                    continue;
                }
                
                // Extract sale details with fallbacks for different API formats
                $sid = $sale['SID'] ?? $sale['sid'] ?? null;
                $quantity = $sale['quantity_sold'] ?? $sale['QuantitySold'] ?? $sale['quantity'] ?? 1;
                
                // Use the book's price for calculating earnings (70% goes to author)
                $bookPrice = $book->price ?? 0;
                $authorEarnings = $bookPrice * $quantity * 0.7;
                
                // Create a unique identifier for this sale record
                // Using a combination of barcode, SID, and potentially invoice ID to identify unique sales
                $uniqueId = md5(($barcode ?? '') . ($productId ?? '') . ($sid ?? '') . ($sale['invoice_id'] ?? $sale['InvoiceID'] ?? ''));
                
                // Check if this sale has already been processed by checking our custom identifier
                $existingTransaction = WalletTransaction::where('meta->erprev_unique_id', $uniqueId)->first();
                
                if ($existingTransaction) {
                    $duplicateCount++;
                    continue;
                }
                
                // Additional check: Look for similar transactions that might be duplicates
                // This helps prevent duplicate entries even if the unique ID generation changes
                $potentialDuplicate = WalletTransaction::where('book_id', $book->id)
                    ->where('type', 'sale')
                    ->where('meta->barcode', $barcode)
                    ->where('meta->quantity_sold', $quantity)
                    ->where('amount', $authorEarnings)
                    ->whereDate('created_at', now()->toDateString())
                    ->first();
                
                if ($potentialDuplicate) {
                    $duplicateCount++;
                    continue;
                }
                
                Log::info('ERPREV Sales Sync - Processing sale', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'isbn' => $book->isbn,
                    'barcode' => $barcode,
                    'product_id' => $productId,
                    'book_price' => $bookPrice,
                    'quantity' => $quantity,
                    'author_earnings' => $authorEarnings
                ]);
                
                // Create wallet transaction for the author using book price (70% to author)
                $transaction = WalletTransaction::create([
                    'user_id' => $book->user_id,
                    'book_id' => $book->id,
                    'type' => 'sale',
                    'amount' => $authorEarnings,
                    'meta' => [
                        'erprev_unique_id' => $uniqueId,
                        'erprev_sid' => $sid,
                        'invoice_id' => $sale['invoice_id'] ?? $sale['InvoiceID'] ?? null,
                        'quantity_sold' => $quantity,
                        'book_price' => $bookPrice,
                        'author_percentage' => 0.7,
                        'author_earnings' => $authorEarnings,
                        'sale_date' => now(), // We'll use current time since no sale date in data
                        'barcode' => $barcode,
                        'product_id' => $productId,
                        'location' => $sale['location'] ?? $sale['Location'] ?? null,
                        'description' => "Sale of {$quantity} copies of '{$book->title}'",
                    ],
                ]);
                
                // Notify the author about the new sale
                $user = User::find($book->user_id);
                if ($user) {
                    // We could send a notification here if needed
                    // $user->notify(new BookSaleNotification($book, $authorEarnings, $quantity));
                }
                
                $processedCount++;
            } catch (\Exception $e) {
                Log::error('Error processing ERPREV sale', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'sale_data' => $sale ?? null
                ]);
                $errorCount++;
            }
        }
        
        $this->info("Sales sync completed. Processed: {$processedCount}, Duplicates: {$duplicateCount}, Books Not Found: {$bookNotFoundCount}, Books Not Accepted: {$bookNotAcceptedCount}, Other Errors: " . ($errorCount - $bookNotFoundCount - $duplicateCount - $bookNotAcceptedCount));
        
        // Log summary
        Log::info('ERPREV Sales Sync Completed', [
            'processed' => $processedCount,
            'duplicates' => $duplicateCount,
            'books_not_found' => $bookNotFoundCount,
            'books_not_accepted' => $bookNotAcceptedCount,
            'other_errors' => ($errorCount - $bookNotFoundCount - $duplicateCount - $bookNotAcceptedCount),
            'filters' => $filters
        ]);
        
        return 0;
    }
}