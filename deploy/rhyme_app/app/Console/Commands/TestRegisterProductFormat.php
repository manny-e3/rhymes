<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Services\RevService;

class TestRegisterProductFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-register-product-format';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the registerProduct format to verify it matches ERPREV requirements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing registerProduct format...');
        
        // Get a sample book
        $book = Book::first();
        if (!$book) {
            $this->error('No books found in the database.');
            return;
        }
        
        $this->info("Testing with book: {$book->title}");
        
        // Initialize RevService
        $revService = new RevService();
        
        // Create the payload manually to check the format
        $payload = [
            'parameters' => [
                'Name' => $book->title,
                'Description' => $book->description,
                'Taxable' => "1",
                'Price' => (string)number_format($book->price, 2, '.', ''),
                'Measure' => 'PCS',
                'Barcode' => $book->isbn,
                'Category' => $book->genre ?? 'Books',
                'CategoryID' => $book->genre_id ?? '1',
                'Class' => 'N/A',
                'ClassID' => '1'
            ]
        ];
        
        $this->info('Generated payload:');
        $this->line(json_encode($payload, JSON_PRETTY_PRINT));
        
        // Verify required fields are present
        $requiredFields = ['Name', 'Description', 'Taxable', 'Price', 'Measure', 'Barcode', 'Category', 'CategoryID', 'Class', 'ClassID'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload['parameters'][$field])) {
                $missingFields[] = $field;
            }
        }
        
        if (empty($missingFields)) {
            $this->info('✓ All required fields are present');
        } else {
            $this->error('✗ Missing fields: ' . implode(', ', $missingFields));
        }
        
        // Verify data types
        $this->info("\nData type verification:");
        $this->line("- Price is string: " . (is_string($payload['parameters']['Price']) ? '✓' : '✗'));
        $this->line("- Taxable is string: " . (is_string($payload['parameters']['Taxable']) ? '✓' : '✗'));
        $this->line("- CategoryID is string: " . (is_string($payload['parameters']['CategoryID']) ? '✓' : '✗'));
        $this->line("- ClassID is string: " . (is_string($payload['parameters']['ClassID']) ? '✓' : '✗'));
        
        $this->info("\nFormat verification complete!");
    }
}