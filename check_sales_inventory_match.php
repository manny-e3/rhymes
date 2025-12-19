<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== Sales & Inventory Matching Check ===\n\n";

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

// Get unique barcodes from sales
$salesBarcodes = [];
foreach ($salesData as $sale) {
    if (isset($sale['Barcode']) && !in_array($sale['Barcode'], $salesBarcodes)) {
        $salesBarcodes[] = $sale['Barcode'];
    }
}

echo "Unique barcodes in sales data: " . count($salesBarcodes) . "\n";
echo "First 5 barcodes: " . implode(', ', array_slice($salesBarcodes, 0, 5)) . "\n\n";

echo "2. Checking inventory data for these barcodes...\n";
$result = $revService->getStockList([]);

if (!$result['success']) {
    echo "Failed to fetch inventory data: " . $result['message'] . "\n";
    exit(1);
}

$inventoryData = $result['data']['records'] ?? [];
echo "Found " . count($inventoryData) . " inventory records\n";

// Check if inventory data has Barcode field
$hasBarcodeField = false;
$hasProductField = false;
$sampleInventory = [];

foreach ($inventoryData as $item) {
    if (isset($item['Barcode'])) {
        $hasBarcodeField = true;
    }
    if (isset($item['Product'])) {
        $hasProductField = true;
        $sampleInventory[] = $item;
    }
    if (count($sampleInventory) >= 3 && $hasBarcodeField && $hasProductField) {
        break;
    }
}

echo "Inventory has Barcode field: " . ($hasBarcodeField ? "YES" : "NO") . "\n";
echo "Inventory has Product field: " . ($hasProductField ? "YES" : "NO") . "\n\n";

if ($hasProductField) {
    echo "Sample inventory records:\n";
    foreach ($sampleInventory as $idx => $item) {
        echo "   {$idx}. Product: " . ($item['Product'] ?? 'N/A') . "\n";
        if (isset($item['UnitCostPrice'])) {
            echo "      UnitCostPrice: {$item['UnitCostPrice']}\n";
        }
        echo "\n";
    }
}

// Try to match sales barcodes with inventory products
echo "3. Attempting to match sales barcodes with inventory...\n";
$matchedProducts = [];

// Let's try a different approach - check if Product field in inventory contains ISBN-like values
foreach ($inventoryData as $item) {
    if (isset($item['Product'])) {
        $product = $item['Product'];
        // Check if any sales barcode matches or is contained in the product name
        foreach ($salesBarcodes as $barcode) {
            if (strpos($product, $barcode) !== false) {
                $matchedProducts[$barcode] = [
                    'product' => $product,
                    'unit_cost_price' => $item['UnitCostPrice'] ?? 'N/A'
                ];
                break;
            }
        }
    }
    
    // Limit our search for performance
    if (count($matchedProducts) >= 5) {
        break;
    }
}

echo "Matched products: " . count($matchedProducts) . "\n";
foreach ($matchedProducts as $barcode => $info) {
    echo "   Barcode {$barcode}: {$info['product']} (UnitCostPrice: {$info['unit_cost_price']})\n";
}

echo "\n=== Check Complete ===\n";