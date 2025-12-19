<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== Detailed Inventory Check ===\n\n";

// Initialize the RevService
$revService = new RevService();

echo "1. Checking a few sample books with ISBNs...\n";
$books = Book::whereNotNull('isbn')->limit(5)->get(['id', 'title', 'isbn']);
foreach ($books as $book) {
    echo "   - ID: {$book->id}, Title: {$book->title}, ISBN: {$book->isbn}\n";
}

echo "\n2. Checking if books are registered in ERPREV...\n";
$registeredBooks = Book::whereNotNull('rev_book_id')->limit(5)->get(['id', 'title', 'isbn', 'rev_book_id']);
echo "Found " . $registeredBooks->count() . " registered books:\n";
foreach ($registeredBooks as $book) {
    echo "   - ID: {$book->id}, Title: {$book->title}, ISBN: {$book->isbn}, ERPREV ID: {$book->rev_book_id}\n";
}

echo "\n3. Fetching detailed inventory data from ERPREV...\n";
// Let's try to get inventory for specific products if we have registered books
if ($registeredBooks->count() > 0) {
    $firstRegisteredBook = $registeredBooks->first();
    echo "Trying to fetch inventory for registered book (ERPREV ID: {$firstRegisteredBook->rev_book_id})...\n";
    $result = $revService->getStockList(['product_id' => $firstRegisteredBook->rev_book_id]);
    
    if ($result['success']) {
        $inventoryData = $result['data']['records'] ?? [];
        echo "Found " . count($inventoryData) . " records for this product\n";
        if (count($inventoryData) > 0) {
            echo "Sample record:\n";
            echo json_encode($inventoryData[0], JSON_PRETTY_PRINT) . "\n\n";
        }
    } else {
        echo "Failed to fetch inventory for specific product: " . $result['message'] . "\n";
    }
}

echo "\n4. Fetching general inventory data...\n";
$result = $revService->getStockList(['limit' => 10]);

if (!$result['success']) {
    echo "Failed to fetch inventory data: " . $result['message'] . "\n";
    exit(1);
}

$inventoryData = $result['data']['records'] ?? [];
echo "Found " . count($inventoryData) . " inventory records\n";

if (count($inventoryData) > 0) {
    echo "\nAnalyzing inventory data structure...\n";
    
    // Check multiple records for patterns
    $fieldStats = [];
    $sampleRecords = min(10, count($inventoryData));
    
    for ($i = 0; $i < $sampleRecords; $i++) {
        $item = $inventoryData[$i];
        foreach ($item as $key => $value) {
            if (!isset($fieldStats[$key])) {
                $fieldStats[$key] = [
                    'count' => 0,
                    'sample_values' => []
                ];
            }
            $fieldStats[$key]['count']++;
            if (count($fieldStats[$key]['sample_values']) < 3) {
                $fieldStats[$key]['sample_values'][] = $value;
            }
        }
    }
    
    echo "Field analysis (from {$sampleRecords} records):\n";
    foreach ($fieldStats as $field => $stats) {
        echo "   - {$field}: appears in {$stats['count']}/{$sampleRecords} records\n";
        echo "     Sample values: " . implode(', ', array_slice($stats['sample_values'], 0, 3)) . "\n";
    }
    
    // Look for fields that might contain identifying information
    echo "\nLooking for identifying fields...\n";
    $identifyingFields = ['Barcode', 'barcode', 'ProductID', 'Product', 'product_id', 'product_code', 'Code', 'code'];
    
    for ($i = 0; $i < min(5, count($inventoryData)); $i++) {
        $item = $inventoryData[$i];
        echo "   Record {$i}:\n";
        foreach ($identifyingFields as $field) {
            if (isset($item[$field])) {
                echo "      {$field}: {$item[$field]}\n";
            }
        }
        echo "\n";
    }
}

echo "\n=== Test Complete ===\n";