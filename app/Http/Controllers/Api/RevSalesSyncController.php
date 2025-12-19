<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RevService;
use App\Models\Book;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RevSalesSyncController extends Controller
{
    private $revService;

    public function __construct(RevService $revService)
    {
        $this->revService = $revService;
    }

    /**
     * Create a mapping of books by their ISBN/Barcode for matching with sales
     *
     * @return array
     */
    private function getBookIsbnMap()
    {
        try {
            // Get all books with ISBN for matching
            $books = Book::whereNotNull('isbn')->get(['id', 'isbn', 'rev_book_id', 'title', 'price', 'status', 'user_id']);
            $isbnMap = [];
            
            foreach ($books as $book) {
                $isbnMap[$book->isbn] = $book;
                // Also add the book to the map using its rev_book_id if available
                if ($book->rev_book_id) {
                    $isbnMap[$book->rev_book_id] = $book;
                }
            }
            
            Log::info('ERPREV Sales Sync - Created ISBN map', [
                'book_count' => count($isbnMap),
                'sample_isbns' => array_slice(array_keys($isbnMap), 0, 5)
            ]);
            
            return $isbnMap;
        } catch (\Exception $e) {
            Log::error('ERPREV Sales Sync - Error creating ISBN map', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Get books inventory data from ERP to get UnitCostPrice
     *
     * @return array
     */
    private function getInventoryData()
    {
        // Not needed for this implementation
        return [];
    }

    /**
     * Sync sales data from ERPREV API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncSales(Request $request)
    {
        try {
            $startTime = microtime(true);
            
            // Validate request parameters
            $request->validate([
                'since' => 'nullable|date',
                'days' => 'nullable|integer|min:1|max:365',
                'book_id' => 'nullable|exists:books,id',
                'debug' => 'nullable|boolean'
            ]);

            // Determine the date range for syncing
            $since = $request->input('since') ? Carbon::parse($request->input('since')) : 
                     ($request->input('days') ? Carbon::now()->subDays($request->input('days')) : Carbon::now()->subDays(7));
                     
            $bookId = $request->input('book_id');
            $debug = $request->input('debug', false);
            
            // Prepare filters for the ERPREV API
            $filters = [
                'date_from' => $since->format('Y-m-d'),
                'date_to' => Carbon::now()->format('Y-m-d'),
            ];
            
            // Add book_id filter if specified
            if ($bookId) {
                $book = Book::find($bookId);
                if ($book) {
                    // We can't filter by product_id effectively, so we'll filter after fetching all data
                }
            }
            
            Log::info('ERPREV Sales Sync API - Starting sync', [
                'filters' => $filters,
                'debug_mode' => $debug
            ]);
            
            // Fetch sales data from ERPREV
            $result = $this->revService->getSalesItems($filters);
            
            if (!$result['success']) {
                Log::error('ERPREV Sales Sync API - Failed to fetch sales data', [
                    'error' => $result['message'],
                    'filters' => $filters
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch sales data: ' . $result['message'],
                    'filters' => $filters
                ], 500);
            }
            
            $salesData = $result['data']['data'] ?? $result['data']['records'] ?? [];
            
            if ($debug) {
                $endTime = microtime(true);
                $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
                
                return response()->json([
                    'success' => true,
                    'message' => 'Debug mode: Found ' . count($salesData) . ' sales records',
                    'record_count' => count($salesData),
                    'sample_data' => count($salesData) > 0 ? $salesData[0] : null,
                    'available_keys' => count($salesData) > 0 ? array_keys($salesData[0]) : [],
                    'filters' => $filters,
                    'execution_time_ms' => round($executionTime, 2)
                ]);
            }
            
            $processedCount = 0;
            $errorCount = 0;
            $duplicateCount = 0;
            $bookNotFoundCount = 0;
            $bookNotAcceptedCount = 0;
            
            // Get book mapping by ISBN for matching
            $bookIsbnMap = $this->getBookIsbnMap();
            
            Log::info('ERPREV Sales Sync - Data maps created', [
                'book_map_size' => count($bookIsbnMap)
            ]);
            
            // Process each sale record
            foreach ($salesData as $sale) {
                try {
                    // Use Barcode from sales data to find book by ISBN
                    $barcode = $sale['Barcode'] ?? $sale['barcode'] ?? null;
                    $productId = $sale['ProductID'] ?? $sale['product_id'] ?? null;
                    
                    if (!$barcode && !$productId) {
                        Log::warning('Missing barcode and product ID in sale record', ['sale' => $sale]);
                        $errorCount++;
                        continue;
                    }
                    
                    // Find the corresponding book in our system using ISBN or ProductID
                    $book = null;
                    if ($barcode && isset($bookIsbnMap[$barcode])) {
                        $book = $bookIsbnMap[$barcode];
                    } elseif ($productId && isset($bookIsbnMap[$productId])) {
                        $book = $bookIsbnMap[$productId];
                    }
                    
                    if (!$book) {
                        Log::warning('Book with ISBN/ProductID not found in system', [
                            'barcode' => $barcode,
                            'product_id' => $productId
                        ]);
                        $bookNotFoundCount++;
                        $errorCount++;
                        continue;
                    }
                    
                    // Check if book has been accepted
                    if ($book->status !== 'accepted' && $book->status !== 'stocked') {
                        Log::warning('Book not accepted or stocked', [
                            'book_id' => $book->id,
                            'book_title' => $book->title,
                            'status' => $book->status
                        ]);
                        $bookNotAcceptedCount++;
                        $errorCount++;
                        continue;
                    }
                    
                    // Ensure the book has a user_id
                    if (!$book->user_id) {
                        Log::warning('Book has no associated user', [
                            'book_id' => $book->id,
                            'book_title' => $book->title,
                            'user_id' => $book->user_id
                        ]);
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
                    $authorEarnings = $bookPrice;
                    
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
                        'author_earnings' => $authorEarnings,
                        'user_id' => $book->user_id
                    ]);
                    
                    // Create wallet transaction for the author using book price (70% to author)
                    $transaction = WalletTransaction::create([
                        'user_id' => $book->user_id, // Explicitly set the user_id
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
                    
                    $processedCount++;
                } catch (\Exception $e) {
                    Log::error('ERPREV Sales Sync API - Error processing sale', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'sale_data' => $sale ?? null
                    ]);
                    $errorCount++;
                }
            }
            
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
            
            // Log summary
            Log::info('ERPREV Sales Sync API - Completed', [
                'processed' => $processedCount,
                'duplicates' => $duplicateCount,
                'books_not_found' => $bookNotFoundCount,
                'books_not_accepted' => $bookNotAcceptedCount,
                'other_errors' => ($errorCount - $bookNotFoundCount - $duplicateCount - $bookNotAcceptedCount),
                'filters' => $filters,
                'execution_time_ms' => round($executionTime, 2)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sales sync completed successfully',
                'statistics' => [
                    'processed' => $processedCount,
                    'duplicates' => $duplicateCount,
                    'books_not_found' => $bookNotFoundCount,
                    'books_not_accepted' => $bookNotAcceptedCount,
                    'other_errors' => ($errorCount - $bookNotFoundCount - $duplicateCount - $bookNotAcceptedCount),
                    'total_records' => count($salesData)
                ],
                'filters' => $filters,
                'execution_time_ms' => round($executionTime, 2)
            ]);
            
        } catch (\Exception $e) {
            Log::error('ERPREV Sales Sync API - Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the status of the last sync operation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncStatus()
    {
        try {
            // Get the latest sync log entries
            $latestLogs = \App\Models\RevSyncLog::where('area', 'sales')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'latest_sync_logs' => $latestLogs
            ]);
        } catch (\Exception $e) {
            Log::error('ERPREV Sales Sync Status API - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sync status: ' . $e->getMessage()
            ], 500);
        }
    }
}