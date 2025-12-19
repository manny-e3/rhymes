<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== Sales Data Structure Check ===\n\n";

// Initialize the RevService
$revService = new RevService();

echo "1. Fetching recent sales data from ERPREV...\n";
$filters = [
    'date_from' => date('Y-m-d', strtotime('-7 days')),
    'date_to' => date('Y-m-d')
];

$result = $revService->getSalesItems($filters);

if (!$result['success']) {
    echo "Failed to fetch sales data: " . $result['message'] . "\n";
    exit(1);
}

$salesData = $result['data']['data'] ?? $result['data']['records'] ?? [];
echo "Found " . count($salesData) . " sales records\n";

if (count($salesData) > 0) {
    echo "\nSample sales record:\n";
    echo json_encode($salesData[0], JSON_PRETTY_PRINT) . "\n\n";
    
    // Check what fields are available in the sales data
    echo "Available fields in sales data:\n";
    $sampleKeys = array_keys($salesData[0]);
    foreach ($sampleKeys as $key) {
        echo "   - {$key}\n";
    }
    echo "\n";
    
    // Look for identifying fields in sales
    echo "Looking for product identification fields in sales...\n";
    $productFields = ['ProductID', 'Product', 'ID', 'Barcode', 'barcode', 'product_id', 'product_code', 'ISBN', 'isbn'];
    foreach ($salesData as $index => $item) {
        if ($index >= 3) break; // Only check first 3 items
        echo "   Sale {$index}:\n";
        foreach ($productFields as $field) {
            if (isset($item[$field])) {
                echo "      {$field}: {$item[$field]}\n";
            }
        }
        echo "\n";
    }
} else {
    echo "No sales data found\n";
}

echo "\n2. Checking inventory data structure again...\n";
$result = $revService->getStockList(['limit' => 5]);

if (!$result['success']) {
    echo "Failed to fetch inventory data: " . $result['message'] . "\n";
    exit(1);
}

$inventoryData = $result['data']['records'] ?? [];
echo "Found " . count($inventoryData) . " inventory records\n";

if (count($inventoryData) > 0) {
    echo "\nSample inventory record:\n";
    echo json_encode($inventoryData[0], JSON_PRETTY_PRINT) . "\n\n";
    
    // Check what fields are available in the inventory data
    echo "Available fields in inventory data:\n";
    $sampleKeys = array_keys($inventoryData[0]);
    foreach ($sampleKeys as $key) {
        echo "   - {$key}\n";
    }
    echo "\n";
    
    // Look for identifying fields in inventory
    echo "Looking for product identification fields in inventory...\n";
    $productFields = ['ProductID', 'Product', 'ID', 'Barcode', 'barcode', 'product_id', 'product_code', 'ISBN', 'isbn'];
    foreach ($inventoryData as $index => $item) {
        if ($index >= 3) break; // Only check first 3 items
        echo "   Inventory {$index}:\n";
        foreach ($productFields as $field) {
            if (isset($item[$field])) {
                echo "      {$field}: {$item[$field]}\n";
            }
        }
        echo "\n";
    }
}

echo "\n=== Check Complete ===\n";