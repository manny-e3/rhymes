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
                                  ->orWhere('meta->erprev_unique_id', $saleId)
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
     * Deep sync sales data from ERPREV API using sold-products-view
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncSalesDeep(Request $request)
    {
        try {
            $startTime = microtime(true);

            // Validate request parameters
            $request->validate([
                'lastupdated' => 'nullable|string',
                'dry_run' => 'nullable|boolean',
                'page_limit' => 'nullable|integer|min:1|max:100',
                'status' => 'nullable|string',
                'source' => 'nullable|string|in:live,local,auto',
                'download' => 'nullable|boolean'
            ]);

            $lastUpdated = $request->input('lastupdated', '1d');
            $dryRun = filter_var($request->input('dry_run', false), FILTER_VALIDATE_BOOLEAN);
            $pageLimit = (int)$request->input('page_limit', 20);
            $targetStatus = $request->input('status', 'all');
            $source = $request->input('source', 'auto');
            $download = filter_var($request->input('download', false), FILTER_VALIDATE_BOOLEAN);

            // Resolve threshold date
            $thresholdDate = $this->resolveThresholdDate($lastUpdated);

            // Determine if we should use local file scan
            $filePath = $this->getSalesHistoryFilePath();
            
            if ($source === 'auto') {
                // If lastupdated is 'all', or a date before June 20, 2026, we use local file to avoid API limitations/timeouts
                if ($lastUpdated === 'all' || 
                    (!preg_match('/^(\d+)([mh])$/i', $lastUpdated) && $thresholdDate->lessThan(Carbon::parse('2026-06-20 00:00:00')))) {
                    $source = 'local';
                } else {
                    $source = 'live';
                }
            }

            // If we need to download first
            if ($download || ($source === 'local' && !file_exists($filePath))) {
                $this->downloadSalesHistory($filePath);
            }

            // 1. Fetch books to match against
            if ($targetStatus === 'all' || in_array('all', explode(',', $targetStatus))) {
                $books = Book::all();
            } else {
                $statuses = explode(',', $targetStatus);
                $books = Book::whereIn('status', $statuses)->get();
            }

            $idMap = [];
            $titleMap = [];
            foreach ($books as $book) {
                if (!empty($book->rev_book_id)) {
                    $idMap[(string)$book->rev_book_id] = $book;
                }
                $titleMap[strtolower(trim($book->title))] = $book;
            }

            // 2. Fetch sales data based on source
            $allSalesData = [];

            if ($source === 'local') {
                if (!file_exists($filePath)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sales history file not found. Run with 'download=1' to download it first.",
                    ], 400);
                }
                Log::info("ERPREV Deep Sales Sync - Scanning local history file", [
                    'file' => $filePath,
                    'threshold_date' => $thresholdDate->toDateTimeString()
                ]);
                $allSalesData = $this->parseLocalSalesFile($filePath, $thresholdDate, $idMap, $titleMap);
            } else {
                // Fetch from live API with pagination
                $hasMore = true;
                $startRow = 0;
                $pageSize = 500;
                $pageCount = 0;

                while ($hasMore && $pageCount < $pageLimit) {
                    $pageCount++;

                    $params = [
                        'lastupdated' => $lastUpdated,
                        'startRow' => $startRow,
                        'TotalRecords' => $pageSize,
                    ];

                    Log::info('ERPREV Deep Sales Sync - Fetching sold products page from live API', [
                        'page' => $pageCount,
                        'startRow' => $startRow,
                        'lastupdated' => $lastUpdated
                    ]);

                    $result = $this->revService->getSoldProductsView($params);

                    if (!$result['success']) {
                        Log::error('ERPREV Deep Sales Sync - Failed to fetch page', [
                            'error' => $result['message'],
                            'page' => $pageCount
                        ]);
                        
                        if (count($allSalesData) > 0) {
                            Log::warning('ERPREV Deep Sales Sync - Proceeding with partial data');
                            break;
                        }

                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to fetch sales data: ' . $result['message'],
                        ], 500);
                    }

                    $records = $result['data']['records'] ?? $result['data']['data'] ?? [];
                    if (empty($records)) {
                        break;
                    }

                    $allSalesData = array_merge($allSalesData, $records);

                    $paginationInfo = $result['data']['pagenation'] ?? $result['data']['pagination'] ?? null;
                    if ($paginationInfo) {
                        $totalRecordsFromApi = isset($paginationInfo['TotalRecords']) ? (int)$paginationInfo['TotalRecords'] : null;
                        $endRow = isset($paginationInfo['endRow']) ? (int)$paginationInfo['endRow'] : (isset($paginationInfo['EndRow']) ? (int)$paginationInfo['EndRow'] : null);

                        if ($endRow !== null && $totalRecordsFromApi !== null && $endRow < $totalRecordsFromApi) {
                            $startRow = $endRow;
                        } else {
                            $hasMore = false;
                        }
                    } else {
                        if (count($records) < $pageSize) {
                            $hasMore = false;
                        } else {
                            $startRow = count($allSalesData);
                        }
                    }
                }
            }

            // 3. Process and import sales
            $processedCount = 0;
            $duplicateCount = 0;
            $bookNotFoundCount = 0;
            $errorCount = 0;
            $totalAuthorEarnings = 0.0;
            $totalPlatformFees = 0.0;

            // Determine platform user ID
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

            $payoutService = app('App\\Services\\PayoutService');

            foreach ($allSalesData as $sale) {
                try {
                    $saleId = (string)($sale['ID'] ?? $sale['id'] ?? '');
                    if (empty($saleId)) {
                        $errorCount++;
                        continue;
                    }

                    // Map the sale to a book
                    $productId = (string)($sale['ProductID'] ?? '');
                    $productName = trim($sale['Product'] ?? '');
                    $normProdName = strtolower($productName);

                    $book = null;
                    if ($productId !== '' && isset($idMap[$productId])) {
                        $book = $idMap[$productId];
                    } elseif ($normProdName !== '' && isset($titleMap[$normProdName])) {
                        $book = $titleMap[$normProdName];
                    }

                    if (!$book) {
                        $bookNotFoundCount++;
                        continue;
                    }

                    $saleDate = Carbon::parse($sale['DateTime'] ?? now());

                    // Check if duplicate transaction already exists in the database
                    $existingTransaction = WalletTransaction::where('type', 'sale')
                        ->where(function($query) use ($saleId) {
                            $query->where('meta->erprev_sale_id', $saleId)
                                  ->orWhere('meta->erprev_sid', $saleId)
                                  ->orWhere('meta->erprev_unique_id', $saleId);
                        })->first();

                    if ($existingTransaction) {
                        $duplicateCount++;
                        if (!$dryRun) {
                            // Update existing transaction dates to match actual sale date
                            $formattedSaleDate = $saleDate->toDateTimeString();
                            $existingCreatedAt = Carbon::parse($existingTransaction->created_at)->toDateTimeString();
                            if ($existingCreatedAt !== $formattedSaleDate) {
                                $existingTransaction->timestamps = false;
                                $existingTransaction->created_at = $saleDate;
                                $existingTransaction->updated_at = $saleDate;
                                $existingTransaction->save();

                                // Also update the platform fee adjustment transaction
                                $platformTx = WalletTransaction::where('type', 'adjustment')
                                    ->where('book_id', $book->id)
                                    ->where('meta->erprev_sale_id', $saleId)
                                    ->first();
                                if ($platformTx) {
                                    $platformTx->timestamps = false;
                                    $platformTx->created_at = $saleDate;
                                    $platformTx->updated_at = $saleDate;
                                    $platformTx->save();
                                }
                            }
                        }
                        continue;
                    }

                    // Extract quantity and price
                    $quantity = (int)str_replace(',', '', $sale['Qty'] ?? '1');
                    $amountStr = $sale['Amount'] ?? '0';
                    $amountStr = str_replace(['₦', ',', ' '], '', $amountStr);
                    $totalAmount = (float)$amountStr;

                    $unitPriceStr = $sale['UnitPrice'] ?? '0';
                    $unitPriceStr = str_replace(['₦', ',', ' '], '', $unitPriceStr);
                    $unitPrice = (float)$unitPriceStr;

                    if ($totalAmount <= 0 && $quantity > 0 && $unitPrice > 0) {
                        $totalAmount = $quantity * $unitPrice;
                    }

                    // Use UnitPrice * Qty as the main and only price (no splitting)
                    $authorEarnings = $unitPrice * $quantity;
                    $platformFee = 0.0;

                    $totalAuthorEarnings += $authorEarnings;
                    $totalPlatformFees += $platformFee;

                    if (!$dryRun) {
                        // Create wallet transaction for the author
                        $authorTx = new WalletTransaction([
                            'user_id' => $book->user_id,
                            'book_id' => $book->id,
                            'type' => 'sale',
                            'amount' => $authorEarnings,
                            'meta' => [
                                'erprev_sale_id' => $saleId,
                                'invoice_id' => $sale['InvoiceID'] ?? $sale['invoice_id'] ?? null,
                                'quantity_sold' => $quantity,
                                'unit_price' => $unitPrice,
                                'total_amount' => $totalAmount,
                                'platform_fee' => $platformFee,
                                'author_earnings' => $authorEarnings,
                                'sale_date' => $saleDate->toDateTimeString(),
                                'location' => $sale['WareHouse'] ?? $sale['location'] ?? null,
                                'description' => "Sale of {$quantity} copies of '{$book->title}' (Imported via Deep Search API)",
                            ],
                        ]);
                        $authorTx->timestamps = false;
                        $authorTx->created_at = $saleDate;
                        $authorTx->updated_at = $saleDate;
                        $authorTx->save();

                        // Update book quantity in database
                        if ($book->quantity !== null) {
                            $book->update(['quantity' => max(0, $book->quantity - $quantity)]);
                        }
                    }

                    $processedCount++;
                } catch (\Exception $e) {
                    Log::error("ERPREV Deep Sales Sync - Record processing error: " . $e->getMessage(), [
                        'sale' => $sale
                    ]);
                    $errorCount++;
                }
            }

            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000;

            Log::info('ERPREV Deep Sales Sync API - Completed', [
                'dry_run' => $dryRun,
                'source' => $source,
                'processed' => $processedCount,
                'duplicates' => $duplicateCount,
                'books_not_found' => $bookNotFoundCount,
                'errors' => $errorCount,
                'execution_time_ms' => round($executionTime, 2)
            ]);

            return response()->json([
                'success' => true,
                'message' => $dryRun ? 'Dry run completed successfully (no database writes)' : 'Sales sync completed successfully',
                'dry_run' => $dryRun,
                'source' => $source,
                'statistics' => [
                    'processed' => $processedCount,
                    'duplicates' => $duplicateCount,
                    'books_not_found' => $bookNotFoundCount,
                    'errors' => $errorCount,
                    'total_records_received' => count($allSalesData),
                    'total_author_earnings' => $totalAuthorEarnings,
                    'total_platform_fees' => $totalPlatformFees
                ],
                'filters' => [
                    'lastupdated' => $lastUpdated,
                    'threshold_date' => $thresholdDate->toDateTimeString(),
                    'target_status' => $targetStatus
                ],
                'execution_time_ms' => round($executionTime, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('ERPREV Deep Sales Sync API - Unexpected error', [
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