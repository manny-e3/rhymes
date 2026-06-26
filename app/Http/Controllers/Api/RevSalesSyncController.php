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
            $books = Book::whereNotNull('isbn')->get(['id', 'isbn', 'rev_book_id', 'title', 'price', 'status', 'user_id', 'quantity']);
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
            
            // Build filters for the ERPREV API using the requested startDate
            $filters = [
                'parameters' => [
                    'startDate' => $since->format('Y-m-d'),
                ],
            ];
            
            // Add book_id filter if specified
            if ($bookId) {
                $book = Book::find($bookId);
                if ($book) {
                    // We can't filter by product_id effectively, so we'll filter after fetching all data
                }
            }
            
            // Fetch sales data from ERPREV with pagination loop to go beyond 5,000 records
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
                
                Log::info('ERPREV Sales Sync API - Fetching sales page', [
                    'page' => $pageCount,
                    'filters' => $currentFilters
                ]);
                
                $result = $this->revService->getSalesItems($currentFilters);
                
                if (!$result['success']) {
                    Log::error('ERPREV Sales Sync API - Failed to fetch sales data', [
                        'error' => $result['message'],
                        'filters' => $currentFilters
                    ]);
                    
                    if (count($allSalesData) > 0) {
                        Log::warning('ERPREV Sales Sync API - Proceeding with partial data due to fetch error on page');
                        break;
                    }
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to fetch sales data: ' . $result['message'],
                        'filters' => $currentFilters
                    ], 500);
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
            
            // Process each sale record
            foreach ($salesData as $sale) {
                try {
                    // Use Barcode from sales data to find book by ISBN
                    $barcode = $sale['Barcode'] ?? $sale['barcode'] ?? null;
                    $productId = $sale['ProductID'] ?? $sale['product_id'] ?? $sale['product']['id'] ?? null;
                    
                    if (!$barcode && !$productId) {
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
                        $bookNotFoundCount++;
                        $errorCount++;
                        continue;
                    }
                    
                    // Check if book has been accepted (Allow all statuses but keep count for stats)
                    if ($book->status !== 'accepted' && $book->status !== 'stocked') {
                        $bookNotAcceptedCount++;
                    }
                    
                    // Ensure the book has a user_id
                    if (!$book->user_id) {
                        $errorCount++;
                        continue;
                    }
                    
                    // If filtering for a specific book, skip others
                    if ($bookId && $book->id != $bookId) {
                        continue;
                    }
                    
                    // Extract sale details with fallbacks for different API formats
                    $saleId = $sale['sale_id'] ?? $sale['SaleID'] ?? $sale['id'] ?? $sale['SID'] ?? $sale['sid'] ?? uniqid();
                    $quantity = $sale['quantity_sold'] ?? $sale['QuantitySold'] ?? $sale['quantity'] ?? 1;
                    $unitPrice = $sale['unit_price'] ?? $sale['UnitPrice'] ?? $sale['price'] ?? $sale['SellingPrice'] ?? 0;
                    $totalAmount = $sale['total_amount'] ?? $sale['TotalAmount'] ?? ($quantity * $unitPrice);
                    $saleDate = $sale['sale_date'] ?? $sale['SaleDate'] ?? $sale['date'] ?? now();
                    
                    if ($unitPrice <= 0) {
                        $errorCount++;
                        continue;
                    }
                    
                    // Create a unique identifier for this sale record
                    $uniqueId = md5(($barcode ?? '') . ($productId ?? '') . ($saleId ?? '') . ($sale['invoice_id'] ?? $sale['InvoiceID'] ?? ''));
                    
                    // Check if this sale has already been processed by checking our custom identifier
                    $existingTransaction = WalletTransaction::where('type', 'sale')
    ->where(function($query) use ($saleId, $uniqueId) {
        $query->where('meta->erprev_sale_id', $saleId)
              ->orWhere('meta->erprev_sid', $saleId)
              ->orWhere('meta->erprev_unique_id', $uniqueId);
    })->first();
                    
                    if ($existingTransaction) {
                        $duplicateCount++;
                        
                        // Pick the date from the sales data to update the wallet record
                        $saleCarbonDate = Carbon::parse($saleDate);
                        $formattedSaleDate = $saleCarbonDate->toDateTimeString();
                        $existingCreatedAt = Carbon::parse($existingTransaction->created_at)->toDateTimeString();
                        
                        if ($existingCreatedAt !== $formattedSaleDate) {
                            $existingTransaction->timestamps = false;
                            $existingTransaction->created_at = $saleCarbonDate;
                            $existingTransaction->updated_at = $saleCarbonDate;
                            $existingTransaction->save();
                            
                            // Also update the platform fee adjustment transaction
                            $platformTx = WalletTransaction::where('type', 'adjustment')
                                ->where('book_id', $book->id)
                                ->where(function($q) use ($saleId, $uniqueId) {
                                    $q->where('meta->erprev_sale_id', $saleId)
                                      ->orWhere('meta->erprev_unique_id', $uniqueId);
                                })->first();
                            if ($platformTx) {
                                $platformTx->timestamps = false;
                                $platformTx->created_at = $saleCarbonDate;
                                $platformTx->updated_at = $saleCarbonDate;
                                $platformTx->save();
                            }
                        }
                        continue;
                    }
                    
                    // Use UnitPrice * Qty as the main and only price (no splitting)
                    $authorEarnings = $unitPrice * $quantity;
                    $platformFee = 0.0;
                    
                    // Additional check: Look for similar transactions that might be duplicates
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
                    
                    // Create wallet transaction for the author using the sale date
                    $saleCarbonDate = Carbon::parse($saleDate);
                    $authorTx = new WalletTransaction([
                        'user_id' => $book->user_id, // Explicitly set the user_id
                        'book_id' => $book->id,
                        'type' => 'sale',
                        'amount' => $authorEarnings,
                        'meta' => [
                            'erprev_unique_id' => $uniqueId,
                            'erprev_sale_id' => $saleId,
                            'erprev_sid' => $saleId,
                            'invoice_id' => $sale['invoice_id'] ?? $sale['InvoiceID'] ?? null,
                            'quantity_sold' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_amount' => $totalAmount,
                            'platform_fee' => $platformFee,
                            'author_earnings' => $authorEarnings,
                            'sale_date' => $saleDate,
                            'barcode' => $barcode,
                            'product_id' => $productId,
                            'location' => $sale['location'] ?? $sale['Location'] ?? null,
                            'description' => "Sale of {$quantity} copies of '{$book->title}'",
                        ],
                    ]);
                    $authorTx->timestamps = false;
                    $authorTx->created_at = $saleCarbonDate;
                    $authorTx->updated_at = $saleCarbonDate;
                    $authorTx->save();

                    if ($book->quantity !== null) {
                        $book->update(['quantity' => max(0, $book->quantity - $quantity)]);
                    }
                    
                    $processedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }
            
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
            
            // Log summary only if there's a change or error
            if ($processedCount > 0 || $errorCount > 0) {
                Log::info('ERPREV Sales Sync API - Completed', [
                    'processed' => $processedCount,
                    'duplicates' => $duplicateCount,
                    'books_not_found' => $bookNotFoundCount,
                    'books_not_accepted' => $bookNotAcceptedCount,
                    'other_errors' => ($errorCount - $bookNotFoundCount - $duplicateCount - $bookNotAcceptedCount),
                    'filters' => $filters,
                    'execution_time_ms' => round($executionTime, 2)
                ]);
            }
            
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
     * Deep sync sales data from ERPREV sold-products-view API into wallet_transactions.
     *
     * POST /api/erprev/sync-sales-deep
     * Params: start_date (required), end_date (optional), dry_run (0|1), start_row (int)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncSalesDeep(Request $request)
    {
        // Extend execution time for large fetches
        set_time_limit(0);
        $startTime = microtime(true);

        try {
            // ── 1. Parse parameters ─────────────────────────────────────────
            $startDate = $request->input('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
            $endDate   = $request->input('end_date');
            $dryRun    = filter_var($request->input('dry_run', false), FILTER_VALIDATE_BOOLEAN);
            $startRow  = max(1, (int)$request->input('start_row', 1));  // allow paging from outside

            // ── 2. Build book lookup maps ────────────────────────────────────
            $books    = Book::all(['id', 'title', 'rev_book_id', 'user_id', 'quantity']);
            $idMap    = [];   // rev_book_id => Book
            $titleMap = [];   // normalised title => Book

            foreach ($books as $book) {
                if (!empty($book->rev_book_id)) {
                    $idMap[(string)$book->rev_book_id] = $book;
                }
                $titleMap[strtolower(trim($book->title))] = $book;
            }

            // ── 3. Build in-memory duplicate map ────────────────────────────
            $existingSaleIds = [];
            WalletTransaction::where('type', 'sale')
                ->select(['id', 'meta'])
                ->chunk(500, function ($txns) use (&$existingSaleIds) {
                    foreach ($txns as $tx) {
                        $meta = $tx->meta;
                        if (is_array($meta) && !empty($meta['erprev_sale_id'])) {
                            $existingSaleIds[(string)$meta['erprev_sale_id']] = $tx;
                        }
                    }
                });

            // ── 4. Fetch one page from the API ───────────────────────────────
            //  The sold-products-view endpoint returns up to 5 000 records per
            //  call when startRow + TotalRecords are sent as strings.
            $apiParams = [
                'startDate'    => $startDate,
                'startRow'     => (string)$startRow,
                'TotalRecords' => '5000',
            ];
            if (!empty($endDate)) {
                $apiParams['stopDate'] = $endDate;
            }

            Log::info('ERPREV syncSalesDeep - Calling sold-products-view', [
                'params' => $apiParams,
                'dry_run' => $dryRun,
            ]);

            $result = $this->revService->getSoldProductsView($apiParams);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch sales from ERPREV: ' . ($result['message'] ?? 'unknown error'),
                ], 500);
            }

            $records       = $result['data']['records'] ?? $result['data']['data'] ?? [];
            $paginationRaw = $result['data']['pagenation'] ?? $result['data']['pagination'] ?? [];
            $totalInApi    = isset($paginationRaw['TotalRecords']) ? (int)$paginationRaw['TotalRecords'] : null;
            $endRowApi     = isset($paginationRaw['endRow'])       ? (int)$paginationRaw['endRow']
                           : (isset($paginationRaw['EndRow'])      ? (int)$paginationRaw['EndRow'] : null);

            Log::info('ERPREV syncSalesDeep - Page fetched', [
                'records_in_page' => count($records),
                'total_in_api'    => $totalInApi,
                'end_row'         => $endRowApi,
            ]);

            // ── 5. Process records and upsert into wallet_transactions ───────
            $inserted        = 0;
            $updated         = 0;
            $bookNotFound    = 0;
            $errors          = 0;
            $totalEarnings   = 0.0;

            foreach ($records as $sale) {
                try {
                    $saleId = (string)($sale['ID'] ?? $sale['id'] ?? '');
                    if ($saleId === '') {
                        $errors++;
                        continue;
                    }

                    // Match book by ProductID (rev_book_id) then by title
                    $productId   = (string)($sale['ProductID'] ?? '');
                    $productName = strtolower(trim($sale['Product'] ?? ''));

                    $book = null;
                    if ($productId !== '' && isset($idMap[$productId])) {
                        $book = $idMap[$productId];
                    } elseif ($productName !== '' && isset($titleMap[$productName])) {
                        $book = $titleMap[$productName];
                    }

                    if (!$book) {
                        $bookNotFound++;
                        continue;
                    }

                    // Parse sale date (format: "01-Apr-26 09:10 AM")
                    $saleDate = Carbon::parse($sale['DateTime'] ?? now());

                    // Parse amounts – strip currency symbols and commas
                    $quantity  = max(1, (int)str_replace(',', '', $sale['Qty'] ?? '1'));
                    $unitPrice = (float)str_replace(['₦', ',', ' ', '&amp;#x20A6;', '&#x20A6;'], '', $sale['UnitPrice'] ?? '0');
                    $amount    = (float)str_replace(['₦', ',', ' ', '&amp;#x20A6;', '&#x20A6;'], '', $sale['Amount']    ?? '0');
                    if ($amount <= 0 && $unitPrice > 0) {
                        $amount = $unitPrice * $quantity;
                    }
                    $authorEarnings = $amount;
                    $totalEarnings += $authorEarnings;

                    $meta = [
                        'erprev_sale_id' => $saleId,
                        'invoice_id'     => $sale['InvoiceID'] ?? null,
                        'product_id'     => $productId,
                        'quantity_sold'  => $quantity,
                        'unit_price'     => $unitPrice,
                        'total_amount'   => $amount,
                        'sale_date'      => $saleDate->toDateTimeString(),
                        'location'       => $sale['WareHouse'] ?? $sale['location'] ?? null,
                        'description'    => "Sale of {$quantity} × '{$book->title}' via ERPREV",
                    ];

                    // Upsert
                    if (isset($existingSaleIds[$saleId])) {
                        // Update existing
                        $updated++;
                        if (!$dryRun) {
                            $tx = WalletTransaction::find($existingSaleIds[$saleId]->id);
                            if ($tx) {
                                $tx->timestamps  = false;
                                $tx->user_id     = $book->user_id;
                                $tx->book_id     = $book->id;
                                $tx->amount      = $authorEarnings;
                                $tx->meta        = $meta;
                                $tx->created_at  = $saleDate;
                                $tx->updated_at  = now();
                                $tx->save();
                            }
                        }
                    } else {
                        // Insert new
                        $inserted++;
                        if (!$dryRun) {
                            $tx = new WalletTransaction([
                                'user_id' => $book->user_id,
                                'book_id' => $book->id,
                                'type'    => 'sale',
                                'amount'  => $authorEarnings,
                                'meta'    => $meta,
                            ]);
                            $tx->timestamps = false;
                            $tx->created_at = $saleDate;
                            $tx->updated_at = $saleDate;
                            $tx->save();

                            // Mark in-memory so same ID won't insert twice within this page
                            $existingSaleIds[$saleId] = $tx;

                             // Decrement book stock — guard against UNSIGNED underflow
                             if ($book->quantity !== null) {
                                 \Illuminate\Support\Facades\DB::table('books')
                                     ->where('id', $book->id)
                                     ->update([
                                         'quantity' => \Illuminate\Support\Facades\DB::raw("GREATEST(0, `quantity` - {$quantity})"),
                                     ]);
                             }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('ERPREV syncSalesDeep - record error', [
                        'sale_id' => $saleId ?? null,
                        'error'   => $e->getMessage(),
                    ]);
                    $errors++;
                }
            }

            $execMs = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('ERPREV syncSalesDeep - Done', [
                'dry_run'       => $dryRun,
                'inserted'      => $inserted,
                'updated'       => $updated,
                'book_not_found'=> $bookNotFound,
                'errors'        => $errors,
                'exec_ms'       => $execMs,
            ]);

            // Determine if there are more pages
            $nextStartRow = null;
            if ($endRowApi !== null && $totalInApi !== null && $endRowApi < $totalInApi) {
                $nextStartRow = $endRowApi + 1;
            }

            return response()->json([
                'success'  => true,
                'dry_run'  => $dryRun,
                'message'  => $dryRun ? 'Dry run — no writes made' : 'Sync completed',
                'statistics' => [
                    'inserted'         => $inserted,
                    'updated'          => $updated,
                    'total_upserted'   => $inserted + $updated,
                    'books_not_found'  => $bookNotFound,
                    'errors'           => $errors,
                    'records_received' => count($records),
                    'total_in_api'     => $totalInApi,
                    'author_earnings'  => $totalEarnings,
                ],
                'pagination' => [
                    'start_row'      => $startRow,
                    'end_row'        => $endRowApi,
                    'total_records'  => $totalInApi,
                    'next_start_row' => $nextStartRow,
                    'has_more'       => $nextStartRow !== null,
                ],
                'filters' => [
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ],
                'execution_time_ms' => $execMs,
            ]);

        } catch (\Exception $e) {
            Log::error('ERPREV syncSalesDeep - Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Resolve lastupdated to a standard threshold date Carbon instance
     *
     * @param string $lastUpdated
     * @return Carbon
     */
    private function resolveThresholdDate($lastUpdated)
    {
        if (empty($lastUpdated) || $lastUpdated === 'all') {
            return Carbon::parse('2018-01-01 00:00:00');
        }

        if (preg_match('/^(\d+)([mhdy])$/i', $lastUpdated, $matches)) {
            $amount = (int)$matches[1];
            $unit = strtolower($matches[2]);
            $date = Carbon::now();
            switch ($unit) {
                case 'm': $date->subMinutes($amount); break;
                case 'h': $date->subHours($amount); break;
                case 'd': $date->subDays($amount); break;
                case 'y': $date->subYears($amount); break;
            }
            return $date;
        }

        try {
            return Carbon::parse($lastUpdated);
        } catch (\Exception $e) {
            return Carbon::parse('2018-01-01 00:00:00');
        }
    }

    /**
     * Find the path to the historical sales JSON file
     *
     * @return string
     */
    private function getSalesHistoryFilePath()
    {
        $paths = [
            'C:\\Users\\aboajah.emmanuel\\.gemini\\antigravity-ide\\scratch\\all_sold_products.json',
            storage_path('app/all_sold_products.json'),
            base_path('all_sold_products.json')
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return storage_path('app/all_sold_products.json');
    }

    /**
     * Download the full ERPRev sales history directly to disk
     *
     * @param string $filePath
     * @return int
     */
    private function downloadSalesHistory($filePath)
    {
        $apiKey = config('services.erprev.api_key');
        $apiSecret = config('services.erprev.api_secret');
        $accountUrl = config('services.erprev.account_url');
        $accountUrl = preg_replace('#^https?://#', '', $accountUrl);
        $authHeader = 'Basic ' . base64_encode($apiKey . ':' . $apiSecret);
        $url = "http://{$accountUrl}/api/1.0/sold-products-view/json/lastupdated/all";

        Log::info("ERPREV Sales Sync - Downloading full sales history to disk", [
            'url' => $url,
            'target_file' => $filePath
        ]);

        $dir = dirname($filePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => $authHeader,
            'Accept' => 'application/json'
        ])->timeout(300)->withOptions([
            'sink' => $filePath
        ])->get($url);

        if (!$response->successful()) {
            throw new \Exception("ERPRev API returned HTTP " . $response->status() . " during history download.");
        }

        $fileSize = filesize($filePath);
        Log::info("ERPREV Sales Sync - Completed download", [
            'file_size_bytes' => $fileSize,
            'file_size_mb' => round($fileSize / (1024 * 1024), 2)
        ]);

        return $fileSize;
    }

    /**
     * Scan local history file chunk-by-chunk for matching sales since threshold date
     *
     * @param string $filePath
     * @param Carbon $thresholdDate
     * @param array $idMap
     * @param array $titleMap
     * @return array
     */
    private function parseLocalSalesFile($filePath, $thresholdDate, $idMap, $titleMap)
    {
        $matchingSales = [];
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Could not open local sales file: {$filePath}");
        }

        $buffer = '';

        while (!feof($handle)) {
            $chunk = fread($handle, 4096000);
            if ($chunk === false) {
                break;
            }
            $buffer .= $chunk;

            preg_match_all('/\{[^{}]+?\}/s', $buffer, $matches, PREG_OFFSET_CAPTURE);

            $lastIndex = 0;
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) {
                    $json = $match[0];
                    $offset = $match[1];

                    $cleanJson = str_replace(["\r", "\n", "\t"], "", $json);
                    $record = json_decode($cleanJson, true);
                    if ($record) {
                        $dateTimeStr = $record['DateTime'] ?? $record['DateTimeWritten'] ?? '';
                        if ($dateTimeStr) {
                            try {
                                $saleDate = Carbon::parse($dateTimeStr);
                                if ($saleDate->greaterThanOrEqualTo($thresholdDate)) {
                                    $productId = (string)($record['ProductID'] ?? '');
                                    $productName = trim($record['Product'] ?? '');
                                    $normProdName = strtolower($productName);

                                    $matched = false;
                                    if ($productId !== '' && isset($idMap[$productId])) {
                                        $matched = true;
                                    } elseif ($normProdName !== '' && isset($titleMap[$normProdName])) {
                                        $matched = true;
                                    }

                                    if ($matched) {
                                        $matchingSales[] = $record;
                                    }
                                }
                            } catch (\Exception $e) {
                                // Ignore date parsing issues
                            }
                        }
                    }
                    $lastIndex = $offset + strlen($json);
                }
            }

            if ($lastIndex > 0) {
                $buffer = substr($buffer, $lastIndex);
            }
        }
        fclose($handle);

        return $matchingSales;
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