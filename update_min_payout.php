<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Update the min_payout_amount to 5000
echo "=== Updating min_payout_amount to 5000 ===\n";

try {
    // Direct database update to avoid the cast issue
    $updated = \App\Models\Setting::where('key', 'min_payout_amount')->update([
        'value' => json_encode(5000),
        'type' => 'integer'
    ]);
    
    if ($updated) {
        echo "Successfully updated min_payout_amount to 5000\n";
    } else {
        echo "Failed to update min_payout_amount\n";
    }
    
    // Verify the update
    $setting = \App\Models\Setting::where('key', 'min_payout_amount')->first();
    if ($setting) {
        echo "Verified value: " . $setting->value . " (Type: " . $setting->type . ")\n";
        echo "Decoded value: " . $setting->value . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "=== Update Completed ===\n";