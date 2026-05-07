<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

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
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\ProfileController;
use App\Models\Setting;
use App\Services\PayoutService;
use App\Services\WalletService;




// Author routes
Route::middleware(['role:author', 'verified'])->group(function () {
    Route::prefix('author')->name('author.')->group(function () {
        // Dashboard
        Route::get('/', [AuthorController::class, 'dashboard'])->name('dashboard');
        
        Route::resource('books', BookController::class);
        Route::post('books/{id}/restore', [BookController::class, 'restore'])->name('books.restore');
        Route::post('books/{book}/retrieval', [BookController::class, 'requestRetrieval'])->name('books.retrieval');
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
Route::middleware(['role:admin', 'verified'])->group(function () {
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
        Route::get('users/{user}/activities', [UserManagementController::class, 'userActivities'])->name('users.activities');
        Route::post('users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
        Route::post('users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
        Route::get('users/export/csv', [UserManagementController::class, 'exportCsv'])->name('users.export.csv');
        Route::get('users/export/pdf', [UserManagementController::class, 'exportPdf'])->name('users.export.pdf');
        
        // Book Management
        Route::get('books', [BookReviewController::class, 'index'])->name('books.index');
        Route::get('books/pending', function() { return app(BookReviewController::class)->index(request()->merge(['status' => 'pending'])); })->name('books.pending');
        Route::get('books/published', function() { return app(BookReviewController::class)->index(request()->merge(['status' => 'accepted'])); })->name('books.published');
        Route::get('books/{book}', [BookReviewController::class, 'show'])->name('books.show');
        Route::patch('books/{book}/review', [BookReviewController::class, 'review'])->name('books.review');
        Route::patch('books/{book}/edit', [BookReviewController::class, 'editBook'])->name('books.edit');
        Route::post('books/{book}/retrieval-action', [BookReviewController::class, 'handleRetrievalAction'])->name('books.retrieval-action');
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
        
        // Email Management
        Route::get('emails', [EmailController::class, 'index'])->name('emails.index');
        Route::get('emails/create', [EmailController::class, 'create'])->name('emails.create');
        Route::post('emails/bulk', [EmailController::class, 'sendBulk'])->name('emails.bulk.send');
        Route::post('emails/authors', [EmailController::class, 'sendToAuthors'])->name('emails.authors.send');
        Route::get('emails/personal/{userId?}', [EmailController::class, 'showPersonalForm'])->name('emails.personal.form');
        Route::post('emails/personal', [EmailController::class, 'sendPersonal'])->name('emails.personal.send');
        
        // Enhanced Email Features
        Route::post('emails/newsletter', [EmailController::class, 'sendNewsletter'])->name('emails.newsletter.send');
        Route::post('emails/announcement', [EmailController::class, 'sendAnnouncement'])->name('emails.announcement.send');
        Route::post('emails/sales-report', [EmailController::class, 'sendSalesReport'])->name('emails.sales-report.send');
        Route::post('emails/bulk-sales-reports', [EmailController::class, 'sendBulkSalesReports'])->name('emails.bulk-sales-reports.send');
        Route::get('emails/logs', [EmailController::class, 'logs'])->name('emails.logs');
        Route::get('emails/logs/{id}', [EmailController::class, 'showLog'])->name('emails.logs.show');
        
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

Route::middleware(['auth', 'verified'])->group(function () {
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