<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RevSalesSyncController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('erprev')->name('api.erprev.')->group(function () {
        Route::match(['GET', 'POST'], '/sync-sales', [RevSalesSyncController::class, 'syncSales'])->name('sync-sales');
        Route::get('/sync-sales/status', [RevSalesSyncController::class, 'syncStatus'])->name('sync-status');
    });
// });