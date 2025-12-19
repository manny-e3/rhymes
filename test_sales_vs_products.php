<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Comparing lastupdated filter behavior between sales and products endpoints...\n";

// Test the RevService with sales filters
$revService = new \App\Services\RevService();

// Test sales with lastupdated filter
echo "\n1. Testing SALES with lastupdated=7d filter:\n";
$salesResult = $revService->getSalesItems(['lastupdated' => '7d']);
echo "Success: " . ($salesResult['success'] ? 'Yes' : 'No') . "\n";
if ($salesResult['success']) {
    echo "Records count: " . count($salesResult['data']['records'] ?? []) . "\n";
}

// Test products with lastupdated filter
echo "\n2. Testing PRODUCTS with lastupdated=7d filter:\n";
$productsResult = $revService->getProductsList(['lastupdated' => '7d']);
echo "Success: " . ($productsResult['success'] ? 'Yes' : 'No') . "\n";
if ($productsResult['success']) {
    echo "Records count: " . count($productsResult['data']['records'] ?? []) . "\n";
}

echo "\nTest completed.\n";