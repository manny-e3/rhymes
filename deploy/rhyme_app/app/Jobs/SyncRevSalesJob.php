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
            
            // Fetch sales data from ERPREV with pagination loop to go beyond 5,000 records
            Log::info('Fetching sales data from ERPREV with pagination', ['filters' => $filters]);
            
            $allSalesData = [];
            $hasMore = true;
            $startRow = 1;
            $previousStartRow = 0;
            $totalRecordsFromApi = null;
            $pageCount = 0;
            $maxPages = 20; // Safe limit to prevent timeouts and memory exhaustion
            
            while ($hasMore && $pageCount < $maxPages) {
                if ($startRow <= $previousStartRow) {
                    Log::warning('ERPREV Sales Sync - Pagination startRow did not advance, breaking loop to prevent infinite loop', [
                        'startRow' => $startRow,
                        'previousStartRow' => $previousStartRow
                    ]);
                    break;
                }
                
                $previousStartRow = $startRow;
                $pageCount++;
                
                $currentFilters = $filters;
                if ($startRow > 1) {
                    $currentFilters['startRow'] = $startRow;
                }
                if ($totalRecordsFromApi !== null) {
                    $currentFilters['TotalRecords'] = $totalRecordsFromApi;
                }
                
                Log::info('ERPREV Sales Sync Job - Fetching sales page', [
                    'page' => $pageCount,
                    'filters' => $currentFilters
                ]);
                
                $result = $revService->getSalesItems($currentFilters);
                
                if (!$result['success']) {
                    Log::error('Failed to fetch sales data from ERPREV', [
                        'error' => $result['message'],
                        'filters' => $currentFilters
                    ]);
                    
                    if (count($allSalesData) > 0) {
                        Log::warning('Proceeding with partial data due to fetch error on page');
                        break;
                    }
                    
                    return;
                }
                
                $records = $result['data']['data'] ?? $result['data']['records'] ?? [];
                $allSalesData = array_merge($allSalesData, $records);
                
                $paginationInfo = $result['data']['pagenation'] ?? $result['data']['pagination'] ?? [];
                $totalRecordsFromApi = isset($paginationInfo['TotalRecords']) ? (int)$paginationInfo['TotalRecords'] : null;
                $endRow = isset($paginationInfo['endRow']) ? (int)$paginationInfo['endRow'] : (isset($paginationInfo['EndRow']) ? (int)$paginationInfo['EndRow'] : null);
                
                if ($endRow !== null && $totalRecordsFromApi !== null && $endRow < $totalRecordsFromApi && count($records) > 0) {
                    $startRow = $endRow + 1;
                } else {
                    $hasMore = false;
                }
            }
            
            $salesData = $allSalesData;
            Log::info('Found sales records to process after pagination', ['count' => count($salesData)]);
            
            $processedCount = 0;
            $errorCount = 0;
            $duplicateCount = 0;
            $totalEarnings = 0;
            
            // Process each sale record
            foreach ($salesData as $sale) {
                try {
                    // Extract book lookup details (Barcode/ISBN and ProductID)
                    $barcode = $sale['Barcode'] ?? $sale['barcode'] ?? null;
                    $productId = $sale['product_id'] ?? $sale['ProductID'] ?? $sale['product']['id'] ?? null;
                    
                    if (!$barcode && !$productId) {
                        Log::warning('Missing barcode and product ID in sale record', ['sale' => $sale]);
                        $errorCount++;
                        continue;
                    }
                    
                    // Find the corresponding book in our system
                    $book = null;
                    if ($barcode) {
                        $book = Book::where('isbn', $barcode)->first();
                    }
                    if (!$book && $productId) {
                        $book = Book::where('rev_book_id', $productId)->first();
                    }
                    
                    if (!$book) {
                        Log::warning('Book not found in system', [
                            'barcode' => $barcode,
                            'product_id' => $productId,
                            'sale' => $sale
                        ]);
                        $errorCount++;
                        continue;
                    }

                    // Check if book has been accepted or stocked
                    if ($book->status !== 'accepted' && $book->status !== 'stocked') {
                        Log::warning('Book is not accepted or stocked, skipping sale', [
                            'book_id' => $book->id,
                            'status' => $book->status
                        ]);
                        $errorCount++;
                        continue;
                    }
                    
                    // Extract sale details with fallbacks for different API formats
                    $saleId = $sale['sale_id'] ?? $sale['SaleID'] ?? $sale['id'] ?? $sale['SID'] ?? $sale['sid'] ?? uniqid();
                    $quantity = $sale['quantity_sold'] ?? $sale['QuantitySold'] ?? $sale['quantity'] ?? 1;
                    $unitPrice = $sale['unit_price'] ?? $sale['UnitPrice'] ?? $sale['price'] ?? $sale['SellingPrice'] ?? 0;
                    $totalAmount = $sale['total_amount'] ?? $sale['TotalAmount'] ?? ($quantity * $unitPrice);
                    $saleDate = $sale['sale_date'] ?? $sale['SaleDate'] ?? $sale['date'] ?? now();
                    
                    // Check if this sale has already been processed (using robust duplicate checks)
                    $existingTransaction = WalletTransaction::where('type', 'sale')
                        ->where(function($query) use ($saleId) {
                            $query->where('meta->erprev_sale_id', $saleId)
                                  ->orWhere('meta->erprev_sid', $saleId)
                                  ->orWhere('meta->erprev_unique_id', $saleId);
                        })->first();
                    
                    if ($existingTransaction) {
                        Log::debug('Sale already processed, skipping', ['sale_id' => $saleId, 'book_id' => $book->id]);
                        $duplicateCount++;
                        continue;
                    }
                    
                    // Calculate author earnings using commission settings from PayoutService
                    $payoutService = app('App\\Services\\PayoutService');
                    $authorEarnings = $payoutService->calculateAuthorEarnings($totalAmount);
                    $platformFee = $payoutService->calculatePlatformFee($totalAmount);
                    
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

                    if ($book->quantity !== null) {
                        $book->update(['quantity' => max(0, $book->quantity - $quantity)]);
                    }
                    
                    // Find the platform/admin user ID to assign the platform fee
                    $platformUserId = 1;
                    try {
                        $admin = User::role('admin')->first();
                        if ($admin) {
                            $platformUserId = $admin->id;
                        } else {
                            $firstUser = User::first();
                            $platformUserId = $firstUser ? $firstUser->id : 1;
                        }
                    } catch (\Exception $e) {
                        $firstUser = User::first();
                        $platformUserId = $firstUser ? $firstUser->id : 1;
                    }

                    // Create a transaction for the platform fee (positive amount on the platform user's wallet)
                    WalletTransaction::create([
                        'user_id' => $platformUserId,
                        'book_id' => $book->id,
                        'type' => 'adjustment',
                        'amount' => $platformFee, // Positive since it is platform revenue
                        'meta' => [
                            'erprev_sale_id' => $saleId,
                            'author_id' => $book->user_id,
                            'quantity_sold' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'platform_fee' => $platformFee,
                            'sale_date' => $saleDate,
                            'description' => "Platform fee from sale of '{$book->title}' (Author ID: {$book->user_id})",
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