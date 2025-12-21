<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ErpRevController;
use App\Http\Controllers\Author\AuthorController;
use App\Http\Controllers\Author\BookController;
use App\Http\Controllers\Author\WalletController;
use App\Http\Controllers\Author\PayoutController;
use App\Http\Controllers\Author\AuthorProfileController;
use App\Http\Controllers\User\BookSubmissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\BookReviewController;
use App\Http\Controllers\Admin\PayoutManagementController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\ProfileController;
use App\Models\Setting;
use App\Services\PayoutService;
use App\Services\WalletService;

// Debug route to test settings
Route::get('/debug-settings', function () {
    // Test direct setting retrieval
    $minPayout = Setting::get('min_payout_amount', 300000);
    $payoutFee = Setting::get('payout_fee', 2.5);
    $frequencyDays = Setting::get('payout_frequency_days', 30);
    $processingMin = Setting::get('payout_processing_time_min', 3);
    $processingMax = Setting::get('payout_processing_time_max', 5);
    
    // Test service retrieval
    $payoutService = new PayoutService(new WalletService());
    $payoutInfo = $payoutService->getPayoutInformation();
    
    return response()->json([
        'direct_retrieval' => [
            'min_payout_amount' => $minPayout,
            'payout_fee' => $payoutFee,
            'payout_frequency_days' => $frequencyDays,
            'payout_processing_time_min' => $processingMin,
            'payout_processing_time_max' => $processingMax,
        ],
        'service_retrieval' => $payoutInfo,
        'all_settings' => Setting::all()->pluck('value', 'key')->toArray()
    ]);
});

// Test route for settings
Route::get('/test-settings-route', function () {
    return response()->json([
        'route_exists' => true,
        'route_url' => route('admin.settings.update'),
        'method' => 'POST'
    ]);
});

// Test route for payout debugging
Route::get('/test-payout-route/{id}', function ($id) {
    try {
        $payout = \App\Models\Payout::findOrFail($id);
        return response()->json([
            'success' => true,
            'payout' => $payout,
            'message' => 'Route working correctly'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'message' => 'Route test failed'
        ], 404);
    }
})->middleware('auth');

// Test route for payout route binding
Route::get('/test-payout-binding/{payout}', function (\App\Models\Payout $payout) {
    return response()->json([
        'success' => true,
        'payout_id' => $payout->id,
        'payout_amount' => $payout->amount_requested,
        'message' => 'Route model binding working correctly'
    ]);
})->middleware('auth');

// Test route for ERPREV integration
Route::get('/test-erprev', function () {
    try {
        $revService = new \App\Services\RevService();
        
        // Test connection
        $connectionResult = $revService->testConnection();
        
        if (!$connectionResult['success']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Connection failed: ' + $connectionResult['message']
            ]);
        }
        
        // Test getting products
        $productsResult = $revService->getProductsList(['limit' => 5]);
        
        if (!$productsResult['success']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product retrieval failed: ' + $productsResult['message']
            ]);
        }
        
        $products = $productsResult['data']['records'] ?? [];
        
        // Test getting inventory
        $inventoryResult = $revService->getStockList(['limit' => 5]);
        
        if (!$inventoryResult['success']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inventory retrieval failed: ' + $inventoryResult['message']
            ]);
        }
        
        $inventory = $inventoryResult['data']['records'] ?? [];
        
        return response()->json([
            'status' => 'success',
            'connection' => $connectionResult,
            'products' => [
                'count' => count($products),
                'data' => $products
            ],
            'inventory' => [
                'count' => count($inventory),
                'data' => $inventory
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exception occurred: ' + $e->getMessage()
        ]);
    }
});

// Test route for ERPREV debugging
Route::get('/test-erprev-debug', function (Request $request, RevService $revService) {
    Log::info('Manual ERPREV test initiated');
    
    // Test connection
    $connectionResult = $revService->testConnection();
    Log::info('Connection test result', $connectionResult);
    
    return response()->json([
        'connection_test' => $connectionResult,
    ]);
});

// Another test route
Route::get('/test-erprev-full', function (Request $request, RevService $revService) {
    Log::info('Full ERPREV test initiated');
    
    // Test connection
    $connectionResult = $revService->testConnection();
    Log::info('Connection test result', $connectionResult);
    
    // Test products
    $productsResult = $revService->getProductsList(['limit' => 5]);
    Log::info('Products test result', [
        'success' => $productsResult['success'],
        'record_count' => count($productsResult['data']['records'] ?? []),
        'sample_data' => count($productsResult['data']['records'] ?? []) > 0 ? $productsResult['data']['records'][0] : null
    ]);
    
    // Test inventory
    $inventoryResult = $revService->getStockList(['limit' => 5]);
    Log::info('Inventory test result', [
        'success' => $inventoryResult['success'],
        'record_count' => count($inventoryResult['data']['records'] ?? []),
        'sample_data' => count($inventoryResult['data']['records'] ?? []) > 0 ? $inventoryResult['data']['records'][0] : null
    ]);
    
    // Test sales
    $salesResult = $revService->getSalesItems(['limit' => 5]);
    Log::info('Sales test result', [
        'success' => $salesResult['success'],
        'record_count' => count($salesResult['data']['records'] ?? []),
        'sample_data' => count($salesResult['data']['records'] ?? []) > 0 ? $salesResult['data']['records'][0] : null
    ]);
    
    return response()->json([
        'connection_test' => $connectionResult,
        'products_test' => $productsResult,
        'inventory_test' => $inventoryResult,
        'sales_test' => $salesResult,
    ]);
});

// Simple test to see if we can get data
Route::get('/test-erprev-simple', function (Request $request, RevService $revService) {
    $result = $revService->getProductsList(['limit' => 3]);
    
    if ($result['success']) {
        return response()->json([
            'success' => true,
            'record_count' => count($result['data']['records']),
            'records' => $result['data']['records']
        ]);
    } else {
        return response()->json([
            'success' => false,
            'error' => $result['message']
        ]);
    }
});

// Test route to see what's being passed to the view
Route::get('/test-erprev-view-data', function (Request $request, RevService $revService) {
    $result = $revService->getProductsList(['limit' => 5]);
    
    if ($result['success']) {
        $products = $result['data']['records'] ?? [];
        $filters = [];
        
        // Log what we're passing to the view
        Log::info('View data test', [
            'product_count' => count($products),
            'products_type' => gettype($products),
            'first_product' => count($products) > 0 ? $products[0] : null,
            'filters' => $filters
        ]);
        
        // Return the same data that would be passed to the view
        return response()->json([
            'products' => $products,
            'filters' => $filters,
            'product_count' => count($products)
        ]);
    } else {
        return response()->json([
            'error' => $result['message']
        ]);
    }
});

// Debug route to test service directly from web
Route::get('/debug-erprev-service', function (Request $request) {
    try {
        // Create service instance directly
        $service = new RevService();
        
        // Test connection
        $connectionResult = $service->testConnection();
        
        // If connection works, try getting some data
        if ($connectionResult['success']) {
            $productsResult = $service->getProductsList(['limit' => 3]);
            $salesResult = $service->getSalesItems(['limit' => 3]);
            
            return response()->json([
                'connection' => $connectionResult,
                'products' => $productsResult,
                'sales' => $salesResult,
            ]);
        } else {
            return response()->json([
                'connection' => $connectionResult,
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// Debug route to check logs
Route::get('/debug-erprev-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    
    if (file_exists($logFile)) {
        // Get last 100 lines of log file
        $lines = file($logFile);
        $lastLines = array_slice($lines, -100);
        
        return response()->json([
            'log_entries' => $lastLines,
            'entry_count' => count($lastLines),
        ]);
    } else {
        return response()->json([
            'error' => 'Log file not found',
            'log_file_path' => $logFile,
        ]);
    }
});

// Debug route to test service directly from web with full logging
Route::get('/debug-erprev-service-full', function (Request $request) {
    try {
        Log::info('=== DEBUG SERVICE FULL TEST STARTED ===');
        
        // Create service instance directly
        $service = new RevService();
        
        // Test connection
        Log::info('Testing connection...');
        $connectionResult = $service->testConnection();
        Log::info('Connection test result', $connectionResult);
        
        // If connection works, try getting sales data
        if ($connectionResult['success']) {
            Log::info('Getting sales data...');
            $salesResult = $service->getSalesItems(['limit' => 3]);
            Log::info('Sales data result', $salesResult);
            
            Log::info('=== DEBUG SERVICE FULL TEST COMPLETED ===');
            
            return response()->json([
                'connection' => $connectionResult,
                'sales' => $salesResult,
            ]);
        } else {
            Log::error('Connection failed', $connectionResult);
            Log::info('=== DEBUG SERVICE FULL TEST COMPLETED WITH CONNECTION ERROR ===');
            
            return response()->json([
                'connection' => $connectionResult,
                'error' => 'Connection failed',
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Exception in debug service test', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        Log::info('=== DEBUG SERVICE FULL TEST COMPLETED WITH EXCEPTION ===');
        
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

// Test route for item categories
Route::get('/test-erprev-categories', function (Request $request, RevService $revService) {
    Log::info('Item categories test initiated');
    
    $result = $revService->getItemCategories();
    
    if ($result['success']) {
        Log::info('Item categories retrieved successfully', [
            'count' => count($result['data']['records'] ?? []),
            'sample' => count($result['data']['records'] ?? []) > 0 ? $result['data']['records'][0] : null
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $result['data'],
            'count' => count($result['data']['records'] ?? [])
        ]);
    } else {
        Log::error('Failed to retrieve item categories', [
            'error' => $result['message']
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $result['message']
        ]);
    }
});

// Test route for user update
Route::get('/test-update', function () {
    $user = \App\Models\User::first();
    if ($user) {
        $userService = new \App\Services\UserService();
        $data = [
            'name' => 'Test User Updated',
            'email' => $user->email,
            'phone' => '1234567890',
            'website' => 'https://example.com',
            'bio' => 'This is a test bio',
            'email_verified' => true
        ];
        
        try {
            $updatedUser = $userService->updateUser($user, $data);
            return response()->json(['success' => true, 'user' => $updatedUser]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    return response()->json(['success' => false, 'error' => 'No user found']);
});

// Test route for user creation
Route::get('/test-create', function () {
    try {
        // Create a role if it doesn't exist
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'author']);
        
        $userService = new \App\Services\UserService();
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => 'author',
            'phone' => '1234567890',
            'website' => 'https://example.com',
            'bio' => 'This is a test bio'
        ];
        
        $user = $userService->createUser($data);
        return response()->json(['success' => true, 'user' => $user]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
});

// Test route for SweetAlert messages
Route::get('/test-sweetalert', function () {
    return redirect()->route('author.books.index')->with('success', 'This is a test success message!');
});

Route::get('/test-sweetalert-error', function () {
    return redirect()->route('author.books.index')->with('error', 'This is a test error message!');
});

// Author routes
Route::middleware('role:author')->group(function () {
    Route::prefix('author')->name('author.')->group(function () {
        // Dashboard
        Route::get('/', [AuthorController::class, 'dashboard'])->name('dashboard');
        
        Route::resource('books', BookController::class);
        Route::post('books/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
        Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
        Route::get('wallet/export', [WalletController::class, 'export'])->name('wallet.export');
        Route::get('payouts', [PayoutController::class, 'index'])->name('payouts.index');
        Route::post('payouts', [PayoutController::class, 'store'])->name('payouts.store');
        Route::get('payouts/payment-details', [PayoutController::class, 'paymentDetails'])->name('payouts.payment-details');
        Route::put('payouts/payment-details', [PayoutController::class, 'updatePaymentDetails'])->name('payouts.payment-details.update');

        Route::get('profile', [AuthorProfileController::class, 'showProfile'])->name('profile.edit');
        Route::patch('profile', [AuthorProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('profile/password', [AuthorProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('profile/avatar', [AuthorProfileController::class, 'uploadAvatar'])->name('profile.avatar.update');
        Route::put('profile/payment-details', [AuthorProfileController::class, 'updatePaymentDetails'])->name('profile.payment-details.update');
        Route::delete('profile', [AuthorProfileController::class, 'deleteAccount'])->name('profile.destroy');
    });
});

// Admin routes
Route::middleware('role:admin')->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboards
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/unified', [AdminController::class, 'unifiedDashboard'])->name('unified-dashboard');
        
        // ERPREV Integration Views
        Route::get('/erprev/sales', [ErpRevController::class, 'salesData'])->name('erprev.sales');
        Route::get('/erprev/inventory', [ErpRevController::class, 'inventoryData'])->name('erprev.inventory');
        Route::get('/erprev/products', [ErpRevController::class, 'productListings'])->name('erprev.products');
        Route::get('/erprev/summary', [ErpRevController::class, 'salesSummary'])->name('erprev.summary');
        Route::get('/erprev/monitoring', [ErpRevController::class, 'syncMonitoring'])->name('erprev.monitoring');
        
        // User Management
        Route::get('users/activity', [AdminController::class, 'userActivity'])->name('users.activity');
        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/trashed', [UserManagementController::class, 'trashed'])->name('users.trashed');
        Route::get('users/authors', [UserManagementController::class, 'authors'])->name('users.authors');
        Route::get('users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        Route::post('users/{user}/promote-author', [UserManagementController::class, 'promoteToAuthor'])->name('users.promote-author');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/{user}/send-verification', [UserManagementController::class, 'sendVerificationEmail'])->name('users.send-verification');
        Route::get('users/{user}/login-as', [UserManagementController::class, 'loginAsUser'])->name('users.login-as');
        Route::get('users/export/csv', [UserManagementController::class, 'exportCsv'])->name('users.export.csv');
        Route::get('users/export/pdf', [UserManagementController::class, 'exportPdf'])->name('users.export.pdf');
        
        // Book Management
        Route::get('books', [BookReviewController::class, 'index'])->name('books.index');
        Route::get('books/pending', function() { return app(BookReviewController::class)->index(request()->merge(['status' => 'pending'])); })->name('books.pending');
        Route::get('books/published', function() { return app(BookReviewController::class)->index(request()->merge(['status' => 'accepted'])); })->name('books.published');
        Route::get('books/{book}', [BookReviewController::class, 'show'])->name('books.show');
        Route::patch('books/{book}/review', [BookReviewController::class, 'review'])->name('books.review');
        Route::post('books/bulk-action', [BookReviewController::class, 'bulkAction'])->name('books.bulk-action');
        Route::get('books/logs', [BookReviewController::class, 'reviewLogs'])->name('books.logs');
        Route::get('books/export/csv', [BookReviewController::class, 'exportCsv'])->name('books.export.csv');
        Route::get('books/export/pdf', [BookReviewController::class, 'exportPdf'])->name('books.export.pdf');
        
        // Payout Management
        Route::get('payouts', [PayoutManagementController::class, 'index'])->name('payouts.index');
        Route::get('payouts/pending', function() { return app(PayoutManagementController::class)->index(request()->merge(['status' => 'pending'])); })->name('payouts.pending');
        Route::get('payouts/completed', function() { return app(PayoutManagementController::class)->index(request()->merge(['status' => 'approved'])); })->name('payouts.completed');
        Route::get('payouts/{payout}', [PayoutManagementController::class, 'show'])->name('payouts.show');
        Route::patch('payouts/{payout}/approve', [PayoutManagementController::class, 'approve'])->name('payouts.approve');
        Route::patch('payouts/{payout}/deny', [PayoutManagementController::class, 'deny'])->name('payouts.deny');
        Route::post('payouts/bulk-action', [PayoutManagementController::class, 'bulkAction'])->name('payouts.bulk-action');
        Route::get('payouts/export/csv', [PayoutManagementController::class, 'exportCsv'])->name('payouts.export.csv');
        Route::get('payouts/export/pdf', [PayoutManagementController::class, 'exportPdf'])->name('payouts.export.pdf');
        
        // Reports & Analytics
        Route::get('reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
        Route::get('reports/sales-dashboard', [ReportsController::class, 'salesDashboard'])->name('reports.sales-dashboard');
        Route::get('reports/analytics', [ReportsController::class, 'analytics'])->name('reports.analytics');
        
        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
        
        // Notifications
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
        Route::post('notifications/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::post('notifications/send-message', [AdminNotificationController::class, 'sendMessage'])->name('notifications.send-message');
        
        // Admin Profile
        Route::get('profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::put('profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
        Route::put('profile/notifications', [AdminProfileController::class, 'updateNotifications'])->name('profile.notifications');
        Route::post('profile/export-data', [AdminProfileController::class, 'exportData'])->name('profile.export-data');
    });
});

Route::get('/', function () {
    // If user is authenticated, redirect to dashboard
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('dashboard');
    }
    
    // Otherwise, show login page
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Notifications
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/toggle-dark-mode', [NotificationController::class, 'toggleDarkMode'])->name('toggle-dark-mode');
    
    // User book submission routes
    Route::get('/books/submit', [BookSubmissionController::class, 'create'])->name('user.books.create');
    Route::post('/books/submit', [BookSubmissionController::class, 'store'])->name('user.books.store');
    
    // User Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notification routes
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// Webhook routes
Route::post('/webhook/erprev', [\App\Http\Controllers\Webhook\ERPRevWebhookController::class, 'handleWebhook'])
    ->name('webhook.erprev')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Error pages illustration route (for development/testing only)
Route::get('/errors/illustration', function () {
    return view('errors.illustration');
})->name('errors.illustration');

// Error pages preview routes (for development/testing only)
Route::get('/errors/{code}', function ($code) {
    // Only allow specific error codes for preview
    $allowedCodes = ['404', '403', '500', '503'];
    
    if (!in_array($code, $allowedCodes)) {
        abort(404);
    }
    
    return response()->view("errors.{$code}", [], (int)$code);
})->name('errors.show');

require __DIR__.'/auth.php';