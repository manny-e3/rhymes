<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Product Listings filters functionality with ERPREV API...\n";

// Test the RevService with product filters
$revService = new \App\Services\RevService();

// Test 1: Without any filter
echo "\n1. Testing without any filter:\n";
$result1 = $revService->getProductsList([]);
echo "Success: " . ($result1['success'] ? 'Yes' : 'No') . "\n";
if ($result1['success']) {
    echo "Records count: " . count($result1['data']['records'] ?? []) . "\n";
}

// Test 2: With Name filter
echo "\n2. Testing with Name=Awkward filter:\n";
$result2 = $revService->getProductsList(['Name' => 'Awkward']);
echo "Success: " . ($result2['success'] ? 'Yes' : 'No') . "\n";
if ($result2['success']) {
    echo "Records count: " . count($result2['data']['records'] ?? []) . "\n";
    // Show first record if available
    if (!empty($result2['data']['records'])) {
        echo "First record name: " . ($result2['data']['records'][0]['Name'] ?? 'N/A') . "\n";
    }
}

// Test 3: With lastupdated filter
echo "\n3. Testing with lastupdated=7d filter:\n";
$result3 = $revService->getProductsList(['lastupdated' => '7d']);
echo "Success: " . ($result3['success'] ? 'Yes' : 'No') . "\n";
if ($result3['success']) {
    echo "Records count: " . count($result3['data']['records'] ?? []) . "\n";
}

// Test 4: Combined filters
echo "\n4. Testing with both lastupdated=7d and Name=Awkward filters:\n";
$result4 = $revService->getProductsList(['lastupdated' => '7d', 'Name' => 'Awkward']);
echo "Success: " . ($result4['success'] ? 'Yes' : 'No') . "\n";
if ($result4['success']) {
    echo "Records count: " . count($result4['data']['records'] ?? []) . "\n";
    // Show first record if available
    if (!empty($result4['data']['records'])) {
        echo "First record name: " . ($result4['data']['records'][0]['Name'] ?? 'N/A') . "\n";
    }
}

echo "\nTest completed.\n";