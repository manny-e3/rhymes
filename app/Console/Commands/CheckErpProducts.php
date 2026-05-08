<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\RevService;
use Illuminate\Console\Command;

class CheckErpProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-erp-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for books that are stocked but not in ERP products';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Starting ERP product check...');
        $this->info('ERP URL: http://y301y.erprev.com/api/1.0/get-products-list/json/');

        // 1. Get stocked books from local database
        $this->info('Fetching books with status "stocked" from local database...');
        $stockedBooks = Book::where('status', 'stocked')->get();
        $stockedCount = $stockedBooks->count();
        $this->info("Found {$stockedCount} stocked books in local database.");

        if ($stockedCount === 0) {
            $this->warn('No stocked books found in local database.');
            return 0;
        }

        // 2. Fetch products from ERP API
        $this->info('Fetching product list from ERP API...');
        $response = $revService->getProductsList();

        if (!$response['success']) {
            $this->error('Failed to fetch products from ERP: ' . $response['message']);
            return 1;
        }

        // The API returns products in 'records' or 'data' based on RevService investigation
        $erpProducts = $response['data']['records'] ?? $response['data']['data'] ?? [];
        $erpCount = count($erpProducts);
        $this->info("Found {$erpCount} products in ERP.");

        if ($erpCount === 0) {
            $this->warn('No products returned from ERP API.');
        }

        // 3. Compare ISBNs with Barcodes
        $this->info('Comparing local ISBNs with ERP Barcodes (normalized)...');
        
        // Create a map of ERP barcodes for efficient lookup
        $erpBarcodes = [];
        foreach ($erpProducts as $product) {
            $barcode = $product['Barcode'] ?? '';
            // Normalize: remove hyphens, spaces and convert to string
            $normalizedBarcode = preg_replace('/[^A-Za-z0-9]/', '', (string)$barcode);
            if (!empty($normalizedBarcode)) {
                $erpBarcodes[$normalizedBarcode] = $product['Name'] ?? 'Unknown';
            }
        }

        $missingBooks = [];
        $foundBooks = [];
        $invalidIsbnCount = 0;

        foreach ($stockedBooks as $book) {
            $isbn = (string)($book->isbn ?? '');
            // Normalize: remove hyphens, spaces
            $normalizedIsbn = preg_replace('/[^A-Za-z0-9]/', '', $isbn);
            
            if (empty($normalizedIsbn) || strtolower($normalizedIsbn) === 'na') {
                $invalidIsbnCount++;
                continue;
            }

            if (isset($erpBarcodes[$normalizedIsbn])) {
                $foundBooks[] = [
                    'id' => $book->id,
                    'title' => $book->title,
                    'isbn' => $book->isbn,
                    'erp_name' => $erpBarcodes[$normalizedIsbn]
                ];
            } else {
                $missingBooks[] = [
                    'id' => $book->id,
                    'title' => $book->title,
                    'isbn' => $book->isbn,
                    'status' => $book->status,
                ];
            }
        }

        // 4. Output results
        $this->info("\n--- Results ---");
        $this->info("Total stocked books: {$stockedCount}");
        $this->info("Books found in ERP: " . count($foundBooks));
        $this->info("Books missing from ERP: " . count($missingBooks));
        $this->info("Books with invalid ISBN (N/A): {$invalidIsbnCount}");

        if (count($foundBooks) > 0) {
            $this->info("\nThe following stocked books WERE found in ERP:");
            foreach ($foundBooks as $fb) {
                $this->line("- ID: {$fb['id']}, Title: {$fb['title']}, ISBN: {$fb['isbn']} (ERP: {$fb['erp_name']})");
            }
        }

        if (count($missingBooks) > 0) {
            $this->error("\nThe following stocked books are NOT found in ERP products list:");
            // Only show first 20 missing books to avoid huge output
            $displayMissing = array_slice($missingBooks, 0, 20);
            $this->table(['ID', 'Title', 'ISBN', 'Status'], $displayMissing);
            if (count($missingBooks) > 20) {
                $this->info("... and " . (count($missingBooks) - 20) . " more missing books.");
            }
            $this->info("\nSummary: " . count($missingBooks) . " out of {$stockedCount} stocked books are missing from ERP.");
        } else {
            $this->info("\nSuccess! All stocked books are present in the ERP products list.");
        }

        // Debug sample ERP barcodes
        $this->info("\nSample ERP Barcodes (first 5):");
        $samples = array_slice($erpBarcodes, 0, 5, true);
        foreach ($samples as $barcode => $name) {
            $this->line("- {$barcode}: {$name}");
        }

        return 0;
    }
}
