<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== UnitCostPrice Verification ===\n\n";

// Initialize the RevService
$revService = new RevService();

echo "1. Getting books with ISBNs...\n";
$books = Book::whereNotNull('isbn')->limit(10)->get(['id', 'title', 'isbn']);
echo "Found " . $books->count() . " books with ISBNs:\n";
foreach ($books as $book) {
    echo "   - ID: {$book->id}, Title: {$book->title}, ISBN: {$book->isbn}\n";
}

echo "\n2. Fetching ALL inventory data from ERPREV...\n";
$result = $revService->getStockList([]);

if (!$result['success']) {
    echo "Failed to fetch inventory data: " . $result['message'] . "\n";
    exit(1);
}

$inventoryData = $result['data']['records'] ?? [];
echo "Found " . count($inventoryData) . " inventory records\n";

// Create a map of inventory data by Barcode
$inventoryMap = [];
$barcodeCount = 0;
$productCount = 0;

foreach ($inventoryData as $item) {
    // Check for Barcode field (case insensitive)
    $barcode = null;
    if (isset($item['Barcode'])) {
        $barcode = $item['Barcode'];
    } elseif (isset($item['barcode'])) {
        $barcode = $item['barcode'];
    }
    
    if ($barcode) {
        $inventoryMap[$barcode] = $item;
        $barcodeCount++;
    }
    
    // Also check for Product field
    if (isset($item['Product'])) {
        $productCount++;
    }
}

echo "   - Records with Barcode field: {$barcodeCount}\n";
echo "   - Records with Product field: {$productCount}\n";

echo "\n3. Checking for ISBN/Barcode matches...\n";
$matchesFound = 0;
$totalUnitCostPrice = 0;
$nonZeroUnitCostPrices = 0;

foreach ($books as $book) {
    $isbn = $book->isbn;
    
    // Check if this ISBN exists in inventory as a Barcode
    if (isset($inventoryMap[$isbn])) {
        $inventoryItem = $inventoryMap[$isbn];
        $unitCostPrice = $inventoryItem['UnitCostPrice'] ?? 'NOT SET';
        
        // Clean up the price format (remove commas and currency symbols)
        $cleanPrice = preg_replace('/[^\d.,]/', '', $unitCostPrice);
        $cleanPrice = str_replace(',', '', $cleanPrice);
        $priceValue = floatval($cleanPrice);
        
        echo "   ✅ MATCH FOUND - Book ID: {$book->id}, ISBN: {$isbn}\n";
        echo "      UnitCostPrice: {$unitCostPrice} (clean: {$priceValue})\n";
        echo "      Product: {$inventoryItem['Product']}\n\n";
        
        $matchesFound++;
        $totalUnitCostPrice += $priceValue;
        if ($priceValue > 0) {
            $nonZeroUnitCostPrices++;
        }
    } else {
        echo "   ❌ NO MATCH - Book ID: {$book->id}, ISBN: {$isbn}\n";
        
        // Let's also check if any partial matches exist
        foreach ($inventoryMap as $barcode => $item) {
            if (strpos($barcode, $isbn) !== false || strpos($isbn, $barcode) !== false) {
                echo "      Partial match with barcode: {$barcode}\n";
                break;
            }
        }
    }
}

echo "\n4. Summary:\n";
echo "   - Total books checked: " . $books->count() . "\n";
echo "   - Matches found: {$matchesFound}\n";
echo "   - Non-zero UnitCostPrice values: {$nonZeroUnitCostPrices}\n";
if ($matchesFound > 0) {
    $avgUnitCostPrice = $totalUnitCostPrice / $matchesFound;
    echo "   - Average UnitCostPrice: {$avgUnitCostPrice}\n";
}

echo "\n=== Verification Complete ===\n";