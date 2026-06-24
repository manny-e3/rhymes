<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\WalletTransaction;

$totalTransactions = WalletTransaction::count();
$saleTransactions = WalletTransaction::where('type', 'sale')->count();

echo "Total Wallet Transactions in Database: {$totalTransactions}\n";
echo "Sale Wallet Transactions: {$saleTransactions}\n\n";

// Let's look at the first 5 sale transactions
$recentSales = WalletTransaction::where('type', 'sale')->orderBy('id', 'desc')->limit(5)->get();
foreach ($recentSales as $t) {
    echo "Transaction ID: {$t->id} | User ID: {$t->user_id} | Book ID: {$t->book_id} | Amount: ₦" . number_format($t->amount, 2) . "\n";
    echo "Meta: " . json_encode($t->meta) . "\n\n";
}

// Let's check if any of our April 2026 Ponmo sales or other sales are already recorded
$sampleSaleId = '690165'; // The Ponmo sale today
$existing = WalletTransaction::where('meta->erprev_sale_id', $sampleSaleId)
    ->orWhere('meta->erprev_unique_id', $sampleSaleId)
    ->first();

if ($existing) {
    echo "Sample sale ID {$sampleSaleId} ALREADY EXISTS in database as transaction ID {$existing->id}.\n";
} else {
    echo "Sample sale ID {$sampleSaleId} does NOT exist in database yet.\n";
}
