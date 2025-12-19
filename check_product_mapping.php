<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

echo "=== Product ID Mapping Check ===\n\n";

// Check registered books
echo "1. Checking registered books...\n";
$registeredBooks = Book::whereNotNull('rev_book_id')->get(['id', 'title', 'isbn', 'rev_book_id']);
echo "Found " . $registeredBooks->count() . " registered books:\n";
foreach ($registeredBooks as $book) {
    echo "   - ID: {$book->id}, Title: {$book->title}\n";
    echo "     ISBN: {$book->isbn}, ERPREV ID: {$book->rev_book_id}\n";
}

echo "\n2. Testing product ID mapping...\n";
if ($registeredBooks->count() > 0) {
    $revService = new RevService();
    
    foreach ($registeredBooks as $book) {
        echo "\n   Checking book ID {$book->id} (ERPREV ID: {$book->rev_book_id}):\n";
        
        // Try to get product details from ERPREV
        echo "   - Fetching product list with filter...\n";
        $result = $revService->getProductsList(['product_id' => $book->rev_book_id]);
        
        if ($result['success']) {
            $products = $result['data']['records'] ?? [];
            echo "     Found " . count($products) . " products\n";
            if (count($products) > 0) {
                echo "     Sample product data:\n";
                echo "       " . json_encode($products[0], JSON_PRETTY_PRINT) . "\n";
                
                // Check if we can find the product ID that matches inventory data
                $productId = $products[0]['ProductID'] ?? $products[0]['product_id'] ?? null;
                if ($productId) {
                    echo "     Product ID for inventory matching: {$productId}\n";
                }
            }
        } else {
            echo "     Failed: " . $result['message'] . "\n";
        }
    }
}

echo "\n=== Test Complete ===\n";