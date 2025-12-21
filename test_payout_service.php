<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the PayoutService
echo "=== Testing Payout Service ===\n";

$payoutService = new \App\Services\PayoutService(new \App\Services\WalletService());

$info = $payoutService->getPayoutInformation();

echo "Payout Information from Service:\n";
echo "Minimum Amount: " . $info['minimum_amount'] . "\n";
echo "Processing Time Min: " . $info['processing_time_min'] . "\n";
echo "Processing Time Max: " . $info['processing_time_max'] . "\n";
echo "Frequency Days: " . $info['frequency_days'] . "\n";
echo "Fee Percentage: " . $info['fee_percentage'] . "\n";

echo "=== Test Completed ===\n";