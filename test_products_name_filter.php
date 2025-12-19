<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Product Listings Name filter...\n";

// Test the RevService with product name filter
$revService = new \App\Services\RevService();

// Test with Name filter
echo "\nTesting with Name=Awkward filter:\n";
$result = $revService->getProductsList(['Name' => 'Awkward']);

echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
if ($result['success']) {
    echo "Records count: " . count($result['data']['records'] ?? []) . "\n";
    // Show first record if available
    if (!empty($result['data']['records'])) {
        echo "First record name: " . ($result['data']['records'][0]['Name'] ?? 'N/A') . "\n";
    }
}

echo "\nTest completed.\n";