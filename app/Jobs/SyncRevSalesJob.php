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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncRevSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [1, 5, 10];
    
    protected $days;
    protected $bookId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($days = 1, $bookId = null)
    {
        $this->days = $days;
        $this->bookId = $bookId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(RevService $revService)
    {
        Log::info('Starting ERPREV Sales Sync Job', [
            'days' => $this->days,
            'book_id' => $this->bookId
        ]);
        
        try {
            // Determine the date range for syncing
            $since = Carbon::now()->subDays($this->days);
            
            // Prepare filters for the ERPREV API
            $filters = [
                'date_from' => $since->format('Y-m-d'),
                'date_to' => Carbon::now()->format('Y-m-d'),
            ];
            
            if ($this->bookId) {
                $book = Book::find($this->bookId);
                if (!$book || !$book->rev_book_id) {
                    Log::error('Book not found or not registered in ERPREV', ['book_id' => $this->bookId]);
                    return;
                }
                $filters['product_id'] = $book->rev_book_id;
            }
            
            // Fetch sales data from ERPREV
            Log::info('Fetching sales data from ERPREV', ['filters' => $filters]);
            $result = $revService->getSalesItems($filters);
            
            if (!$result['success']) {
                Log::error('Failed to fetch sales data', ['error' => $result['message']]);
                return;
            }
            
            $salesData = $result['data']['data'] ?? $result['data']['records'] ?? [];
            Log::info('Found sales records to process', ['count' => count($salesData)]);
            
            $processedCount = 0;
            $errorCount = 0;
            $duplicateCount = 0;
            $totalEarnings = 0;
            
            // Process each sale record
            foreach ($salesData as $sale) {
                try {
                    // Find the corresponding book in our system
                    $productId = $sale['product_id'] ?? $sale['ProductID'] ?? $sale['product']['id'] ?? null;
                    
                    if (!$productId) {
                        Log::warning('Missing product ID in sale record', ['sale' => $sale]);
                        $errorCount++;
                        continue;
                    }
                    
                    $book = Book::where('rev_book_id', $productId)->first();
                    
                    if (!$book) {
                        Log::warning('Book with ERPREV product ID not found in system', ['product_id' => $productId]);
                        $errorCount++;
                        continue;
                    }
                    
                    // Extract sale details with fallbacks for different API formats
                    $saleId = $sale['sale_id'] ?? $sale['SaleID'] ?? $sale['id'] ?? uniqid();
                    $quantity = $sale['quantity_sold'] ?? $sale['QuantitySold'] ?? $sale['quantity'] ?? 1;
                    $unitPrice = $sale['unit_price'] ?? $sale['UnitPrice'] ?? $sale['price'] ?? 0;
                    $totalAmount = $sale['total_amount'] ?? $sale['TotalAmount'] ?? ($quantity * $unitPrice);
                    $saleDate = $sale['sale_date'] ?? $sale['SaleDate'] ?? $sale['date'] ?? now();
                    
                    // Check if this sale has already been processed
                    $existingTransaction = WalletTransaction::where('meta->erprev_sale_id', $saleId)->first();
                    
                    if ($existingTransaction) {
                        Log::debug('Sale already processed, skipping', ['sale_id' => $saleId, 'book_id' => $book->id]);
                        $duplicateCount++;
                        continue;
                    }
                    
                    // Calculate author earnings (70% goes to author, 30% to platform)
                    $authorEarnings = $totalAmount * 0.7;
                    $platformFee = $totalAmount * 0.3;
                    
                    // Create wallet transaction for the author
                    $transaction = WalletTransaction::create([
                        'user_id' => $book->user_id,
                        'book_id' => $book->id,
                        'type' => 'sale',
                        'amount' => $authorEarnings,
                        'meta' => [
                            'erprev_sale_id' => $saleId,
                            'invoice_id' => $sale['invoice_id'] ?? $sale['InvoiceID'] ?? null,
                            'quantity_sold' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'platform_fee' => $platformFee,
                            'author_earnings' => $authorEarnings,
                            'sale_date' => $saleDate,
                            'location' => $sale['location'] ?? $sale['Location'] ?? null,
                            'description' => "Sale of {$quantity} copies of '{$book->title}'",
                        ],
                    ]);
                    
                    // Also create a transaction for the platform fee
                    WalletTransaction::create([
                        'user_id' => $book->user_id, // Platform user ID - you might want to change this
                        'book_id' => $book->id,
                        'type' => 'platform_fee',
                        'amount' => -$platformFee, // Negative because it's a deduction
                        'meta' => [
                            'erprev_sale_id' => $saleId,
                            'author_id' => $book->user_id,
                            'quantity_sold' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'platform_fee' => $platformFee,
                            'sale_date' => $saleDate,
                            'description' => "Platform fee for sale of '{$book->title}'",
                        ],
                    ]);
                    
                    // Update total earnings
                    $totalEarnings += $authorEarnings;
                    
                    // Log the successful processing
                    Log::info('Processed sale for author', [
                        'author_id' => $book->user_id,
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'sale_id' => $saleId,
                        'quantity' => $quantity,
                        'total_amount' => $totalAmount,
                        'author_earnings' => $authorEarnings,
                        'platform_fee' => $platformFee
                    ]);
                    
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
            
            Log::info('ERPREV Sales Sync Job Completed', [
                'processed' => $processedCount,
                'duplicates' => $duplicateCount,
                'errors' => $errorCount,
                'total_earnings' => $totalEarnings,
                'total_sales_value' => $totalEarnings / 0.7 // Reverse calculation to get total sales
            ]);
            
            // If we processed any sales, we might want to notify authors
            if ($processedCount > 0) {
                Log::info('Notifying authors of new sales', ['count' => $processedCount]);
                // You could add notification logic here
            }
            
        } catch (\Exception $e) {
            Log::error('ERPREV Sales Sync Job Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw to allow retries
        }
    }
}