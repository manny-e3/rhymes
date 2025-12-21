<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test retrieving settings directly
echo "=== Testing Setting Retrieval ===\n";

$minPayout = \App\Models\Setting::get('min_payout_amount', 300000);
echo "Min Payout Amount (via Setting::get): " . $minPayout . " (Type: " . gettype($minPayout) . ")\n";

$payoutFee = \App\Models\Setting::get('payout_fee', 2.5);
echo "Payout Fee (via Setting::get): " . $payoutFee . " (Type: " . gettype($payoutFee) . ")\n";

$frequencyDays = \App\Models\Setting::get('payout_frequency_days', 30);
echo "Frequency Days (via Setting::get): " . $frequencyDays . " (Type: " . gettype($frequencyDays) . ")\n";

$processingMin = \App\Models\Setting::get('payout_processing_time_min', 3);
echo "Processing Min (via Setting::get): " . $processingMin . " (Type: " . gettype($processingMin) . ")\n";

$processingMax = \App\Models\Setting::get('payout_processing_time_max', 5);
echo "Processing Max (via Setting::get): " . $processingMax . " (Type: " . gettype($processingMax) . ")\n";

// Test retrieving individual settings
echo "\n=== Testing Individual Setting Retrieval ===\n";
$setting = \App\Models\Setting::where('key', 'min_payout_amount')->first();
if ($setting) {
    echo "Direct DB retrieval - Min Payout Amount: " . $setting->value . " (Type: " . gettype($setting->value) . ")\n";
}

echo "=== Test Completed ===\n";