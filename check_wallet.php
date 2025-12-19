<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel's database component
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WalletTransaction;

echo "Checking wallet transactions...\n";
$totalTransactions = WalletTransaction::count();
echo "Total wallet transactions: " . $totalTransactions . "\n";

$saleTransactions = WalletTransaction::where('type', 'sale')->count();
echo "Sale transactions: " . $saleTransactions . "\n";

// Show recent sale transactions
echo "\nRecent sale transactions:\n";
$recentSales = WalletTransaction::where('type', 'sale')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get(['id', 'user_id', 'book_id', 'type', 'amount', 'created_at']);

foreach ($recentSales as $transaction) {
    echo "- ID: {$transaction->id}, User: {$transaction->user_id}, Book: {$transaction->book_id}, Amount: {$transaction->amount}, Date: {$transaction->created_at}\n";
}