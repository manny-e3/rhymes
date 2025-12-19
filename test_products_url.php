<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Product Listings URL construction...\n";

// Test the RevService with product filters
$revService = new \App\Services\RevService();

// Test with lastupdated filter
echo "\nTesting with lastupdated=7d filter:\n";
$result = $revService->getProductsList(['lastupdated' => '7d']);

echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
if ($result['success']) {
    echo "Records count: " . count($result['data']['records'] ?? []) . "\n";
}

echo "\nCheck the laravel.log file for the actual URL that was called.\n";