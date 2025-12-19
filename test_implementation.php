<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== Implementation Test ===\n\n";

// Initialize the RevService
$revService = new RevService();

echo "1. Testing book and inventory matching logic...\n";

// Simulate the book ISBN map
$bookIsbnMap = [
    '9780241217931' => (object)[
        'id' => 1,
        'title' => '#Girlboss PB',
        'isbn' => '9780241217931',
        'user_id' => 1
    ],
    '9789789924387' => (object)[
        'id' => 2,
        'title' => 'Pre-Order Book #2',
        'isbn' => '9789789924387',
        'user_id' => 1
    ]
];

echo "Book ISBN Map:\n";
foreach ($bookIsbnMap as $isbn => $book) {
    echo "   - ISBN: {$isbn}, Title: {$book->title}\n";
}

// Simulate inventory data map
$inventoryDataMap = [
    '#Girlboss PB' => [
        'Product' => '#Girlboss PB',
        'UnitCostPrice' => '8,835.25',
        'UnitsInStock' => 5
    ],
    'Ace of Spades PB' => [
        'Product' => 'Ace of Spades PB',
        'UnitCostPrice' => '7,500.00',
        'UnitsInStock' => 3
    ]
];

echo "\nInventory Data Map:\n";
foreach ($inventoryDataMap as $product => $item) {
    echo "   - Product: {$product}, UnitCostPrice: {$item['UnitCostPrice']}\n";
}

echo "\n2. Testing matching logic...\n";

// Simulate a sale record
$sale = [
    'Barcode' => '9780241217931',
    'SID' => 'SALE001',
    'quantity_sold' => 2,
    'SellingPrice' => 5000,
    'total_amount' => 10000
];

$barcode = $sale['Barcode'];

echo "Processing sale with barcode: {$barcode}\n";

// Find book by ISBN
if (!isset($bookIsbnMap[$barcode])) {
    echo "   ❌ Book not found in system\n";
} else {
    $book = $bookIsbnMap[$barcode];
    echo "   ✅ Book found: {$book->title}\n";
    
    // Find inventory item by matching product name with book title
    $inventoryItem = null;
    foreach ($inventoryDataMap as $productName => $item) {
        if (stripos($productName, $book->title) !== false || stripos($productName, $barcode) !== false) {
            $inventoryItem = $item;
            break;
        }
    }
    
    if (!$inventoryItem) {
        echo "   ❌ Inventory data not found for book\n";
    } else {
        echo "   ✅ Inventory data found\n";
        
        // Extract clean unit cost price
        $rawUnitCostPrice = $inventoryItem['UnitCostPrice'];
        $unitCostPrice = preg_replace('/[^\d.]/', '', $rawUnitCostPrice);
        
        echo "   Raw UnitCostPrice: {$rawUnitCostPrice}\n";
        echo "   Clean UnitCostPrice: {$unitCostPrice}\n";
        
        // Calculate earnings using RAW unitCostPrice (no percentages)
        $quantity = $sale['quantity_sold'];
        $authorEarnings = $unitCostPrice * $quantity;
        
        echo "   Quantity: {$quantity}\n";
        echo "   Author Earnings (RAW): {$authorEarnings}\n";
        
        echo "\n   🎉 SUCCESS: Would create wallet transaction with amount: {$authorEarnings}\n";
    }
}

echo "\n=== Test Complete ===\n";