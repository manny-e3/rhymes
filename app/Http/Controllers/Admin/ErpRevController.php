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

         Log::info('ERPREV Controller - salesData called', [
            'filters' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
        ]);
        
        // Get the lastupdated filter parameter
        $lastUpdated = $request->get('lastupdated', '');
        
        // Get the name search parameter
        $nameSearch = $request->get('name', '');
        
        $filters = [];
        
        // Add lastupdated filter if provided and valid
        $validLastUpdatedValues = ['', 'all', '5m', '10m', '30m', '1h', '4h', '6h', '24h', '7d', '30d', '60d', '100d'];
        if (in_array($lastUpdated, $validLastUpdatedValues) && $lastUpdated !== '') {
            $filters['lastupdated'] = $lastUpdated;
        }
        
        // Add name search filter if provided
        if (!empty($nameSearch)) {
            $filters['Name'] = $nameSearch;
        }
        
        // For sales data, we'll fetch all records and paginate on our side
        // This ensures we have full control over pagination
        Log::info('ERPREV Controller - Calling getSalesItems with filters', [
            'filters' => $filters,
        ]);
        
        $result = $this->revService->getSalesItems($filters);
        
        Log::info('ERPREV Controller - salesData result received', [
            'success' => $result['success'] ?? false,
            'has_data' => isset($result['data']),
            'data_keys' => isset($result['data']) ? array_keys($result['data']) : [],
            'message' => $result['message'] ?? 'N/A',
        ]);
        
        if (!$result['success']) {
            Log::error('ERPREV Controller - salesData failed', [
                'message' => $result['message'] ?? 'Unknown error',
                'full_result' => $result,
            ]);
            
            return back()->with('error', 'Failed to fetch sales data: ' . $result['message']);
        }
        
        // Extract all records from the response
        $allSalesData = $result['data']['records'] ?? [];
        
        // Extract pagination info from ERPREV response
        $paginationInfo = $result['data']['pagenation'] ?? [];
        $totalRecords = (int)($paginationInfo['TotalRecords'] ?? count($allSalesData));
        
        // Log pagination info for debugging
        Log::info('ERPREV Controller - Pagination Info', [
            'pagination_info' => $paginationInfo,
            'total_records_from_api' => $totalRecords,
            'records_received' => count($allSalesData),
            'actual_records_count' => count($allSalesData), // Add this for clarity
        ]);
        
        // Implement our own pagination with 100 records per page
        $perPage = 100;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        // Slice the data to show only the records for the current page
        $salesData = array_slice($allSalesData, $offset, $perPage);
        
        // Create a paginator manually since we're getting data from an external API
        $currentPage = $page;
        
        // Use the actual count of records received for pagination, not the TotalRecords from API
        // This fixes the issue where filtered results were showing incorrect counts
        $totalRecords = count($allSalesData);
        
        // Create a simple pagination object
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $salesData, // Only the sliced data for current page
            $totalRecords, // Total records from API
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );
        
        Log::info('ERPREV Controller - salesData processed', [
            'total_record_count' => count($allSalesData),
            'displayed_record_count' => count($salesData),
            'total_records' => $totalRecords,
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'sample_record' => count($salesData) > 0 ? $salesData[0] : null,
        ]);
        
        // Pass the filters to the view
        $filters['lastupdated'] = $lastUpdated;
        $filters['name'] = $nameSearch;
        
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
        
        // Get the product search parameter
        $productSearch = $request->get('product', '');
        
        $filters = [];
        
        // Add lastupdated filter if provided and valid
        $validLastUpdatedValues = ['', 'all', '5m', '10m', '30m', '1h', '4h', '6h', '24h', '7d', '30d', '60d', '100d'];
        if (in_array($lastUpdated, $validLastUpdatedValues) && $lastUpdated !== '') {
            $filters['lastupdated'] = $lastUpdated;
        }
        
        // Add product search filter if provided
        if (!empty($productSearch)) {
            $filters['Product'] = $productSearch;
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
        $allInventoryData = $result['data']['records'] ?? [];
        
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
        
        // Get the name search parameter (lastupdated doesn't work for products)
        $nameSearch = $request->get('name', '');
        
        $filters = [];
        
        // Add name search filter if provided
        if (!empty($nameSearch)) {
            $filters['Name'] = $nameSearch;
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
        $allProducts = $result['data']['records'] ?? [];
        
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
        $filters['name'] = $nameSearch;
        
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
}