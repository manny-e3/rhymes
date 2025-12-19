<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the RevService with lastupdated filter
$revService = new \App\Services\RevService();

echo "Testing RevService with lastupdated filter...\n";

// Test 1: Without filter
echo "\n1. Testing without filter:\n";
$result1 = $revService->getSalesItems([]);
echo "Success: " . ($result1['success'] ? 'Yes' : 'No') . "\n";
if ($result1['success']) {
    echo "Records count: " . count($result1['data']['records'] ?? []) . "\n";
}

// Test 2: With lastupdated filter
echo "\n2. Testing with lastupdated=7d filter:\n";
$result2 = $revService->getSalesItems(['lastupdated' => '7d']);
echo "Success: " . ($result2['success'] ? 'Yes' : 'No') . "\n";
if ($result2['success']) {
    echo "Records count: " . count($result2['data']['records'] ?? []) . "\n";
}

echo "\nTest completed.\n";