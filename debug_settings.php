<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if settings exist
echo "=== Checking Settings Table ===\n";
try {
    $settings = \App\Models\Setting::all();
    echo "Total settings in database: " . $settings->count() . "\n";
    
    if ($settings->count() > 0) {
        foreach ($settings as $setting) {
            echo "Key: " . $setting->key . " | Value: " . (is_array($setting->value) ? json_encode($setting->value) : $setting->value) . " | Type: " . $setting->type . "\n";
        }
    } else {
        echo "No settings found in database\n";
    }
    
    echo "\n=== Checking Specific Payout Settings ===\n";
    $payoutSettings = [
        'min_payout_amount',
        'payout_fee',
        'payout_frequency_days',
        'payout_processing_time_min',
        'payout_processing_time_max'
    ];
    
    foreach ($payoutSettings as $key) {
        $value = \App\Models\Setting::get($key, 'NOT_FOUND');
        echo "$key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== Debug Completed ===\n";