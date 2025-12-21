<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the payout settings
echo "=== Payout Settings Test ===\n";

$minPayoutAmount = config('app.min_payout_amount', 300000);
$payoutFee = config('app.payout_fee', 2.5);
$frequencyDays = config('app.payout_frequency_days', 30);
$processingMin = config('app.payout_processing_time_min', 3);
$processingMax = config('app.payout_processing_time_max', 5);

echo "Minimum Payout Amount: " . $minPayoutAmount . "\n";
echo "Payout Fee: " . $payoutFee . "%\n";
echo "Frequency Days: " . $frequencyDays . "\n";
echo "Processing Time Min: " . $processingMin . " days\n";
echo "Processing Time Max: " . $processingMax . " days\n";

echo "=== Test Completed ===\n";