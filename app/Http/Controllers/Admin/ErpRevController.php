<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RevService;
use App\Models\RevSyncLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ErpRevController extends Controller
{
    private $revService;

    public function __construct(RevService $revService)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->revService = $revService;
        Log::info('ErpRevController constructed', [
            'service_instance' => get_class($revService),
        ]);
    }

    /**
     * Display sync operations monitoring dashboard
     */
    public function syncMonitoring(Request $request)
    {
        Log::info('ERPREV Controller - syncMonitoring called');
        
        // Get filter parameters
        $area = $request->get('area');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Build query for sync logs
        $query = RevSyncLog::orderBy('created_at', 'desc');
        
        // Apply filters
        if ($area) {
            $query->where('area', $area);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }
        
        // Get paginated logs
        $logs = $query->paginate(20);
        
        // Get summary statistics
        $summary = $this->getSyncSummary();
        
        // Get recent error logs
        $recentErrors = RevSyncLog::where('status', 'error')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.erprev.monitoring', compact('logs', 'summary', 'recentErrors'));
    }
    
    /**
     * Get sync operation summary statistics
     */
    private function getSyncSummary()
    {
        // Total sync operations
        $totalSyncs = RevSyncLog::count();
        
        // Successful sync operations
        $successfulSyncs = RevSyncLog::where('status', 'success')->count();
        
        // Failed sync operations
        $failedSyncs = RevSyncLog::where('status', 'error')->count();
        
        // Success rate
        $successRate = $totalSyncs > 0 ? ($successfulSyncs / $totalSyncs) * 100 : 0;
        
        // Sync operations by area
        $syncsByArea = RevSyncLog::select('area')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('area')
            ->get()
            ->keyBy('area');
        
        // Recent sync operations (last 24 hours)
        $recentSyncs = RevSyncLog::where('created_at', '>=', now()->subDay())
            ->select('status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');
        
        return [
            'total' => $totalSyncs,
            'successful' => $successfulSyncs,
            'failed' => $failedSyncs,
            'success_rate' => round($successRate, 2),
            'by_area' => $syncsByArea,
            'recent' => $recentSyncs
        ];
    }

    /**
     * Display sales data from ERPREV
     */
    public function salesData(Request $request)
    {
        Log::info('ERPREV Controller - salesData called (API mode)', [
            'filters' => $request->all(),
        ]);

        // Get filter parameters
        $lastUpdated = $request->get('lastupdated', '2026-04-01');
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $nameSearch = trim($request->get('name', ''));
        $invoiceSearch = trim($request->get('invoice_id', ''));

        $filters = [];
        
        // Custom date range takes precedence over lastupdated select menu
        if ($startDate !== '') {
            $filters['startDate'] = $startDate;
            if ($endDate !== '') {
                $filters['stopDate'] = $endDate;
            }
        } else {
            // Pass lastupdated filter
            if ($lastUpdated !== '') {
                $filters['lastupdated'] = $lastUpdated;
            }
        }

        // Pass InvoiceID to the API if set
        if ($invoiceSearch !== '') {
            $filters['InvoiceID'] = $invoiceSearch;
        }

        Log::info('ERPREV Controller - Calling getSoldProductsView with filters', [
            'filters' => $filters,
        ]);

        $result = $this->revService->getSoldProductsView($filters);

        Log::info('ERPREV Controller - salesData result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);

        if (!$result['success']) {
            Log::error('ERPREV Controller - salesData failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);

            return back()->with('error', 'Failed to fetch sales data: ' . $result['message']);
        }

        // Extract all records from the response
        $allSalesData = $result['data']['records'] ?? $result['data']['data'] ?? [];

        // Local filtering fallback
        if (!empty($nameSearch) || !empty($invoiceSearch)) {
            $allSalesData = array_filter($allSalesData, function($item) use ($nameSearch, $invoiceSearch) {
                $match = true;
                if (!empty($nameSearch)) {
                    $itemName = $item['Product'] ?? $item['product'] ?? $item['Name'] ?? '';
                    $match = $match && stripos($itemName, $nameSearch) !== false;
                }
                if (!empty($invoiceSearch)) {
                    $itemInvoiceId = $item['InvoiceID'] ?? $item['invoice_id'] ?? '';
                    $match = $match && ($itemInvoiceId == $invoiceSearch);
                }
                return $match;
            });
            $allSalesData = array_values($allSalesData);
        }

        // Map elements to match what sales.blade.php expects
        $mapped = collect($allSalesData)->map(function ($item) {
            return [
                'ID'             => $item['ID'] ?? $item['id'] ?? 'N/A',
                'InvoiceID'      => $item['InvoiceID'] ?? $item['invoice_id'] ?? 'N/A',
                'DateTime'       => $item['DateTime'] ?? $item['date_time'] ?? $item['created_at'] ?? 'N/A',
                'Product'        => $item['Product'] ?? $item['product'] ?? 'N/A',
                'ProductID'      => $item['ProductID'] ?? $item['product_id'] ?? '',
                'Category'       => $item['Category'] ?? $item['category'] ?? 'N/A',
                'WareHouse'      => $item['WareHouse'] ?? $item['warehouse'] ?? $item['Location'] ?? 'N/A',
                'Qty'            => $item['Qty'] ?? $item['qty'] ?? $item['quantity'] ?? 1,
                'UnitPrice'      => $item['UnitPrice'] ?? $item['unit_price'] ?? 0,
                'Amount'         => $item['Amount'] ?? $item['amount'] ?? $item['TotalAmount'] ?? 0,
                'Currency'       => '&#x20A6;',
                'CustomerName'   => $item['CustomerName'] ?? $item['customer_name'] ?? 'N/A',
                'CustomerMobile' => $item['CustomerMobile'] ?? $item['customer_mobile'] ?? '',
            ];
        })->toArray();

        // Implement pagination with 100 records per page
        $perPage = 100;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;

        // Slice the data to show only the records for the current page
        $slicedData = array_slice($mapped, $offset, $perPage);
        $totalRecords = count($mapped);

        // Create the paginator manually
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $slicedData,
            $totalRecords,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $filters = [
            'lastupdated' => $lastUpdated,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'name'        => $nameSearch,
            'invoice_id'  => $invoiceSearch,
        ];

        Log::info('ERPREV Controller - salesData processed (API)', [
            'total'    => $totalRecords,
            'per_page' => $perPage,
            'page'     => $page,
        ]);

        return view('admin.erprev.sales', compact('paginator', 'filters'));
    }

    /**
     * Display inventory data from ERPREV
     */
    public function inventoryData(Request $request)
    {
        Log::info('ERPREV Controller - inventoryData called', [
            'filters' => $request->all(),
        ]);
        
        // Get the lastupdated filter parameter
        $lastUpdated = $request->get('lastupdated', '');
        
        // Get search parameters
        $productSearch = $request->get('product', '');
        $barcodeSearch = $request->get('barcode', '');
        
        $filters = [];
        
        // Only pass lastupdated to the ERP API URL.
        // Product/Barcode are handled locally to support multi-word searches.
        $validLastUpdatedValues = ['', 'all', '5m', '10m', '30m', '1h', '4h', '6h', '24h', '7d', '30d', '60d', '100d'];
        if (in_array($lastUpdated, $validLastUpdatedValues) && $lastUpdated !== '') {
            $filters['lastupdated'] = $lastUpdated;
        }

        // Pass Barcode or Product filter directly to the API if searched, to ensure we get results even if
        // the item is outside the default record limit.
        if (!empty($barcodeSearch)) {
            $filters['Barcode'] = $barcodeSearch;
        } elseif (!empty($productSearch)) {
            $keyword = $this->getBestSearchKeyword($productSearch);
            if (!empty($keyword)) {
                $filters['Product'] = $keyword;
            }
        }
        
        Log::info('ERPREV Controller - Calling getStockList with filters', [
            'filters' => $filters,
        ]);
        
        $result = $this->revService->getStockList($filters);
        
        Log::info('ERPREV Controller - inventoryData result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - inventoryData failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);
            
            return back()->with('error', 'Failed to fetch inventory data: ' . $result['message']);
        }
        
        // Extract all records from the response
        $allInventoryData = $result['data']['records'] ?? $result['data']['data'] ?? [];
        
        // Local filtering fallback
        $barcodeSearch = $request->get('barcode');
        if (!empty($productSearch) || !empty($barcodeSearch)) {
            $allInventoryData = array_filter($allInventoryData, function($item) use ($productSearch, $barcodeSearch) {
                $match = true;
                if (!empty($productSearch)) {
                    $itemName = $item['Product'] ?? $item['product'] ?? $item['Name'] ?? '';
                    $match = $match && stripos($itemName, $productSearch) !== false;
                }
                if (!empty($barcodeSearch)) {
                    $itemBarcode = $item['Barcode'] ?? $item['barcode'] ?? '';
                    $match = $match && ($itemBarcode == $barcodeSearch);
                }
                return $match;
            });
            $allInventoryData = array_values($allInventoryData);
        }
        
        // Extract pagination info from ERPREV response
        $paginationInfo = $result['data']['pagenation'] ?? [];
        $totalRecords = (int)($paginationInfo['TotalRecords'] ?? count($allInventoryData));
        
        // Log pagination info for debugging
        Log::info('ERPREV Controller - Inventory Pagination Info', [
            'pagination_info' => $paginationInfo,
            'total_records_from_api' => $totalRecords,
            'records_received' => count($allInventoryData),
            'actual_records_count' => count($allInventoryData), // Add this for clarity
        ]);
        
        // Implement our own pagination with 100 records per page
        $perPage = 100;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        // Slice the data to show only the records for the current page
        $inventoryData = array_slice($allInventoryData, $offset, $perPage);
        
        // Create a paginator manually since we're getting data from an external API
        $currentPage = $page;
        
        // Use the actual count of records received for pagination, not the TotalRecords from API
        // This fixes the issue where filtered results were showing incorrect counts
        $totalRecords = count($allInventoryData);
        
        // Create a simple pagination object
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $inventoryData, // Only the sliced data for current page
            $totalRecords, // Total records from API
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        Log::info('ERPREV Controller - inventoryData processed', [
            'total_record_count' => count($allInventoryData),
            'displayed_record_count' => count($inventoryData),
            'total_records' => $totalRecords,
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'sample_record' => count($inventoryData) > 0 ? $inventoryData[0] : null,
        ]);
        
        // Pass the filters to the view
        $filters['lastupdated'] = $lastUpdated;
        $filters['product'] = $productSearch;
        $filters['barcode'] = $request->get('barcode');
        
        return view('admin.erprev.inventory', compact('paginator', 'filters'));
    }

    /**
     * Display product listings from ERPREV
     */
    public function productListings(Request $request)
    {
        Log::info('ERPREV Controller - productListings called', [
            'filters' => $request->all(),
        ]);
        
        // Get search parameters
        $idSearch = $request->get('id', '');
        $nameSearch = $request->get('name', '');
        $barcodeSearch = $request->get('barcode', '');
        
        $filters = [];
        
        // Pass ID, Barcode, or Name filter directly to the API if searched, to ensure we get results even if
        // the item is outside the default record limit.
        if (!empty($idSearch)) {
            $filters['ID'] = $idSearch;
        } elseif (!empty($barcodeSearch)) {
            $filters['Barcode'] = $barcodeSearch;
        } elseif (!empty($nameSearch)) {
            $keyword = $this->getBestSearchKeyword($nameSearch);
            if (!empty($keyword)) {
                $filters['Name'] = $keyword;
            }
        }
        
        Log::info('ERPREV Controller - Calling getProductsList with filters', [
            'filters' => $filters,
        ]);
        
        $result = $this->revService->getProductsList($filters);
        
        Log::info('ERPREV Controller - productListings result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - productListings failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);
            
            return back()->with('error', 'Failed to fetch product listings: ' . $result['message']);
        }
        
        // Extract all records from the response
        $allProducts = $result['data']['records'] ?? $result['data']['data'] ?? [];
        
        // Local filtering fallback
        if (!empty($idSearch) || !empty($nameSearch) || !empty($barcodeSearch)) {
            $allProducts = array_filter($allProducts, function($item) use ($idSearch, $nameSearch, $barcodeSearch) {
                $match = true;
                if (!empty($idSearch)) {
                    $itemId = $item['ID'] ?? $item['id'] ?? '';
                    $match = $match && ($itemId == $idSearch);
                }
                if (!empty($nameSearch)) {
                    $itemName = $item['Name'] ?? $item['name'] ?? '';
                    $match = $match && stripos($itemName, $nameSearch) !== false;
                }
                if (!empty($barcodeSearch)) {
                    $itemBarcode = $item['Barcode'] ?? $item['barcode'] ?? '';
                    $match = $match && ($itemBarcode == $barcodeSearch);
                }
                return $match;
            });
            $allProducts = array_values($allProducts);
        }
        
        // Extract pagination info from ERPREV response
        $paginationInfo = $result['data']['pagenation'] ?? [];
        $totalRecords = (int)($paginationInfo['TotalRecords'] ?? count($allProducts));
        
        // Log pagination info for debugging
        Log::info('ERPREV Controller - Products Pagination Info', [
            'pagination_info' => $paginationInfo,
            'total_records_from_api' => $totalRecords,
            'records_received' => count($allProducts),
            'actual_records_count' => count($allProducts), // Add this for clarity
        ]);
        
        // Implement our own pagination with 100 records per page
        $perPage = 100;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        // Slice the data to show only the records for the current page
        $products = array_slice($allProducts, $offset, $perPage);
        
        // Create a paginator manually since we're getting data from an external API
        $currentPage = $page;
        
        // Use the actual count of records received for pagination, not the TotalRecords from API
        // This fixes the issue where filtered results were showing incorrect counts
        $totalRecords = count($allProducts);
        
        // Create a simple pagination object
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $products, // Only the sliced data for current page
            $totalRecords, // Total records from API
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        Log::info('ERPREV Controller - productListings processed', [
            'total_record_count' => count($allProducts),
            'displayed_record_count' => count($products),
            'total_records' => $totalRecords,
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'sample_record' => count($products) > 0 ? $products[0] : null,
        ]);
        
        // Pass the filters to the view
        $filters['id'] = $idSearch;
        $filters['name'] = $nameSearch;
        $filters['barcode'] = $barcodeSearch;
        
        return view('admin.erprev.products', compact('paginator', 'filters'));
    }

    /**
     * Display sales summary from ERPREV
     */
    public function salesSummary(Request $request)
    {
        Log::info('ERPREV Controller - salesSummary called', [
            'filters' => $request->all(),
        ]);
        
        $filters = [];
        
        Log::info('ERPREV Controller - Calling getSoldProductsSummary with filters', [
            'filters' => $filters,
        ]);
        
        $result = $this->revService->getSoldProductsSummary($filters);
        
        Log::info('ERPREV Controller - salesSummary result', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - salesSummary failed', [
                'message' => $result['message'] ?? 'Unknown error',
            ]);
            
            return back()->with('error', 'Failed to fetch sales summary: ' . $result['message']);
        }
        
        // Extract all records from the response
        $allSummaryData = $result['data']['records'] ?? [];
        
        // Extract pagination info from ERPREV response
        $paginationInfo = $result['data']['pagenation'] ?? [];
        $totalRecords = (int)($paginationInfo['TotalRecords'] ?? count($allSummaryData));
        
        // Implement our own pagination with 100 records per page
        $perPage = 100;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        // Slice the data to show only the records for the current page
        $summaryData = array_slice($allSummaryData, $offset, $perPage);
        
        // Create a paginator manually since we're getting data from an external API
        $currentPage = $page;
        
        // Create a simple pagination object
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $summaryData, // Only the sliced data for current page
            $totalRecords, // Total records from API
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        Log::info('ERPREV Controller - salesSummary processed', [
            'total_record_count' => count($allSummaryData),
            'displayed_record_count' => count($summaryData),
            'total_records' => $totalRecords,
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'sample_record' => count($summaryData) > 0 ? $summaryData[0] : null,
        ]);
        
        return view('admin.erprev.summary', compact('paginator', 'filters'));
    }

    /**
     * Display the endpoint tester view
     */
    public function testEndpoints(Request $request)
    {
        $endpoints = [
            'get-invoices' => 'Sales/Invoice View',
            'sold-products-view' => 'Sold Products View',
            'rendered-services-view' => 'Sold/Rendered Services View',
            'get-quotations' => 'Order/Quotation View',
            'quotation-products-view' => 'Order/Quotation Products View',
            'quotation-services-view' => 'Order/Quotation Services View',
        ];

        return view('admin.erprev.test_endpoints', compact('endpoints'));
    }

    /**
     * Run the selected endpoint test
     */
    public function runTestEndpoint(Request $request)
    {
        // Increase PHP execution time limit to allow cURL timeout (120s) to fire first
        @set_time_limit(240);

        $request->validate([
            'endpoint' => 'required|string',
            'lastupdated' => 'nullable|string',
            'startRow' => 'nullable|integer|min:0',
            'TotalRecords' => 'nullable|integer|min:0',
            'ProductID' => 'nullable|string',
            'Product' => 'nullable|string',
        ]);

        $endpoint = $request->input('endpoint');
        $filters = [
            'lastupdated' => $request->input('lastupdated', 'all'),
        ];

        if ($request->filled('startRow')) {
            $filters['startRow'] = $request->input('startRow');
        }

        if ($request->filled('TotalRecords')) {
            $filters['TotalRecords'] = $request->input('TotalRecords');
        }

        if ($request->filled('ProductID')) {
            $filters['ProductID'] = $request->input('ProductID');
        }

        if ($request->filled('Product')) {
            $filters['Product'] = $request->input('Product');
        }

        $result = $this->revService->getEndpointData($endpoint, $filters);

        $endpoints = [
            'get-invoices' => 'Sales/Invoice View',
            'sold-products-view' => 'Sold Products View',
            'rendered-services-view' => 'Sold/Rendered Services View',
            'get-quotations' => 'Order/Quotation View',
            'quotation-products-view' => 'Order/Quotation Products View',
            'quotation-services-view' => 'Order/Quotation Services View',
        ];

        $selectedEndpoint = $endpoint;
        $selectedLastUpdated = $filters['lastupdated'];
        $selectedStartRow = $request->input('startRow');
        $selectedTotalRecords = $request->input('TotalRecords');
        $selectedProductID = $request->input('ProductID');
        $selectedProduct = $request->input('Product');

        return view('admin.erprev.test_endpoints', compact(
            'endpoints', 
            'result', 
            'selectedEndpoint', 
            'selectedLastUpdated', 
            'selectedStartRow', 
            'selectedTotalRecords',
            'selectedProductID',
            'selectedProduct'
        ));
    }

    /**
     * Get the best alphanumeric keyword for ERPREV search
     * to avoid API URL routing breakage.
     */
    private function getBestSearchKeyword($string)
    {
        if (preg_match_all('/[a-zA-Z0-9]+/', $string, $matches)) {
            $words = $matches[0];
            foreach ($words as $word) {
                if (strlen($word) >= 3) {
                    return $word;
                }
            }
            if (count($words) > 0) {
                return $words[0];
            }
        }
        return '';
    }
}