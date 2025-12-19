<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== Barcode/ISBN Matching Test ===\n\n";

// Initialize the RevService
$revService = new RevService();

echo "1. Checking books with ISBNs...\n";
$books = Book::whereNotNull('isbn')->limit(10)->get(['id', 'title', 'isbn']);
echo "Found " . $books->count() . " books with ISBNs:\n";
foreach ($books as $book) {
    echo "   - ID: {$book->id}, Title: {$book->title}, ISBN: {$book->isbn}\n";
}

echo "\n2. Fetching inventory data from ERPREV...\n";
$result = $revService->getStockList([]);

if (!$result['success']) {
    echo "Failed to fetch inventory data: " . $result['message'] . "\n";
    exit(1);
}

$inventoryData = $result['data']['records'] ?? [];
echo "Found " . count($inventoryData) . " inventory records\n";

if (count($inventoryData) > 0) {
    echo "Sample inventory record:\n";
    echo json_encode($inventoryData[0], JSON_PRETTY_PRINT) . "\n\n";
    
    // Check what fields are available in the inventory data
    echo "Available fields in inventory data:\n";
    $sampleKeys = array_keys($inventoryData[0]);
    foreach ($sampleKeys as $key) {
        echo "   - {$key}\n";
    }
    echo "\n";
    
    // Look for fields that might contain barcode-like information
    echo "Looking for product identification fields...\n";
    $productFields = ['ProductID', 'Product', 'ID', 'Barcode', 'barcode', 'product_id', 'product_code'];
    foreach ($inventoryData as $index => $item) {
        if ($index >= 5) break; // Only check first 5 items
        echo "   Item {$index}:\n";
        foreach ($productFields as $field) {
            if (isset($item[$field])) {
                echo "      {$field}: {$item[$field]}\n";
            }
        }
        echo "\n";
    }
    
    // Try to find matching books by different possible fields
    echo "3. Testing book matching with different fields...\n";
    foreach ($books as $book) {
        $isbn = $book->isbn;
        $found = false;
        
        // Try to match by Product field (which might contain the title or ISBN)
        foreach ($inventoryData as $item) {
            $product = $item['Product'] ?? '';
            if (strpos($product, $isbn) !== false || strpos($isbn, $product) !== false) {
                echo "   ✅ PRODUCT MATCH - Book ID: {$book->id}, ISBN: {$isbn}\n";
                echo "      Product: {$product}\n";
                $unitCostPrice = $item['UnitCostPrice'] ?? 'NOT FOUND';
                echo "      UnitCostPrice: {$unitCostPrice}\n\n";
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo "   ❌ NO MATCH - Book ID: {$book->id}, ISBN: {$isbn}\n";
        }
    }
} else {
    echo "No inventory data found\n";
}

echo "\n=== Test Complete ===\n";