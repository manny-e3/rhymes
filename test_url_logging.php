<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing URL logging with lastupdated filter...\n";

// Test the RevService with lastupdated filter
$revService = new \App\Services\RevService();

// Test with lastupdated filter
echo "\nTesting with lastupdated=7d filter:\n";
$result = $revService->getSalesItems(['lastupdated' => '7d']);

echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
if ($result['success']) {
    echo "Records count: " . count($result['data']['records'] ?? []) . "\n";
} else {
    echo "Error: " . $result['message'] . "\n";
}

echo "\nCheck the laravel.log file for detailed logging information.\n";