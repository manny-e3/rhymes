<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if settings table exists and has data
try {
    $settingsCount = \App\Models\Setting::count();
    echo "Settings count: " . $settingsCount . "\n";
    
    if ($settingsCount > 0) {
        echo "=== Existing Settings ===\n";
        $settings = \App\Models\Setting::all();
        foreach ($settings as $setting) {
            echo $setting->key . ": " . (is_array($setting->value) ? json_encode($setting->value) : $setting->value) . "\n";
        }
    } else {
        echo "No settings found in database\n";
    }
    
    // Check specific payout settings
    echo "\n=== Checking Payout Settings ===\n";
    $payoutKeys = [
        'min_payout_amount',
        'payout_fee',
        'payout_frequency_days',
        'payout_processing_time_min',
        'payout_processing_time_max'
    ];
    
    foreach ($payoutKeys as $key) {
        $value = \App\Models\Setting::get($key);
        echo "$key: " . ($value !== null ? $value : 'Not set') . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== Test Completed ===\n";