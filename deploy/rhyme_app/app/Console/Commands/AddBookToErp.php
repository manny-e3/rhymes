<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Console\Command;

class AddBookToErp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-book-to-erp {isbn}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if a book exists in ERP and add it if missing';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $isbn = $this->argument('isbn');
        // Normalize ISBN for comparison
        $normalizedIsbn = preg_replace('/[^A-Za-z0-9]/', '', $isbn);

        $this->info("Checking ERP for book with ISBN: {$isbn}");

        // 1. Find the book locally
        $book = Book::where('isbn', $isbn)
            ->orWhereRaw("REPLACE(REPLACE(isbn, '-', ''), ' ', '') = ?", [$normalizedIsbn])
            ->first();

        if (!$book) {
            $this->error("Book with ISBN {$isbn} not found in local database.");
            return 1;
        }

        $this->info("Found local book: '{$book->title}' (ID: {$book->id}, Status: {$book->status})");

        // 2. Check if it already exists in ERP
        $this->info("Checking ERP for existing product with this barcode...");
        $response = $revService->getProductsList(['Barcode' => $isbn]);

        if (!$response['success']) {
            $this->error("Failed to check ERP: " . $response['message']);
            return 1;
        }

        $erpProducts = $response['data']['records'] ?? [];
        $existingProduct = null;

        if (count($erpProducts) > 0) {
            // Find exact match just in case
            foreach ($erpProducts as $product) {
                $erpBarcode = preg_replace('/[^A-Za-z0-9]/', '', (string)($product['Barcode'] ?? ''));
                if ($erpBarcode === $normalizedIsbn) {
                    $existingProduct = $product;
                    break;
                }
            }
        }

        if ($existingProduct) {
            $this->warn("Book already exists in ERP!");
            $this->line("ERP Name: " . ($existingProduct['Name'] ?? 'N/A'));
            $this->line("ERP Barcode: " . ($existingProduct['Barcode'] ?? 'N/A'));
            
            // Get the correct field for product ID (TransactionID usually)
            $erpId = $existingProduct['TransactionID'] ?? $existingProduct['ProductID'] ?? null;
            
            if ($erpId) {
                $this->line("ERP ID: {$erpId}");
                if ($book->rev_book_id !== $erpId) {
                    $book->update(['rev_book_id' => $erpId]);
                    $this->info("Updated local database with ERP ID.");
                }
            }
            return 0;
        }

        // 3. Register the product if not found
        $this->info("Book not found in ERP. Registering product...");
        
        $result = $revService->registerProduct($book);

        if ($result['success']) {
            $productId = $result['product_id'];
            $this->info("✓ Success! Book registered in ERP.");
            $this->line("Assigned ERP ID: {$productId}");
            
            // Update local book record
            $book->update(['rev_book_id' => $productId]);
            $this->info("Local record updated with ERP ID.");
        } else {
            $this->error("✗ Failed to register book: " . $result['message']);
            return 1;
        }

        return 0;
    }
}
