<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check all settings
echo "=== All Settings ===\n";
$settings = \App\Models\Setting::all();
foreach ($settings as $setting) {
    echo $setting->key . ": " . (is_array($setting->value) ? json_encode($setting->value) : $setting->value) . "\n";
}

echo "\n=== Payout Settings ===\n";
$payoutSettings = [
    'min_payout_amount',
    'payout_fee',
    'payout_frequency_days',
    'payout_processing_time_min',
    'payout_processing_time_max'
];

foreach ($payoutSettings as $key) {
    $value = \App\Models\Setting::get('app.' . $key);
    echo "app.$key: " . ($value ?? 'Not set') . "\n";
}

echo "=== Test Completed ===\n";