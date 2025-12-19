<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\WalletTransaction;

// Test the wallet balance calculation
echo "Testing wallet balance calculation...\n";

// Create a test user
$user = User::factory()->create([
    'name' => 'Test User',
    'email' => 'test@example.com'
]);

// Add a sale transaction
WalletTransaction::create([
    'user_id' => $user->id,
    'type' => 'sale',
    'amount' => 100.00,
    'meta' => ['description' => 'Test sale']
]);

echo "Balance after sale: " . $user->getWalletBalance() . "\n";

// Add a payout transaction (negative amount)
WalletTransaction::create([
    'user_id' => $user->id,
    'type' => 'payout',
    'amount' => -25.00,
    'meta' => ['description' => 'Test payout']
]);

echo "Balance after payout: " . $user->getWalletBalance() . "\n";

// Clean up
$user->delete();

echo "Test completed successfully!\n";