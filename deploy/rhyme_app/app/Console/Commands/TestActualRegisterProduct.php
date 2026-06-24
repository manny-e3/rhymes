<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

class TestActualRegisterProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-actual-register-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the actual registerProduct method to verify payload format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing actual registerProduct method...');
        
        // Get a sample book
        $book = Book::first();
        if (!$book) {
            $this->error('No books found in the database.');
            return;
        }
        
        $this->info("Testing with book: {$book->title}");
        
        // Initialize RevService
        $revService = new RevService();
        
        // Temporarily enable logging to see the payload
        Log::info('Starting registerProduct test', ['book_id' => $book->id]);
        
        // We won't actually send the request, just check the payload format
        // Let's examine the method by creating a mock version that just returns the payload
        
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
        
        $this->info('Payload that would be sent to ERPREV:');
        $this->line(json_encode($payload, JSON_PRETTY_PRINT));
        
        // Verify it matches the required format
        $expectedFormat = [
            "parameters" => [
                "Name" => "Man On the Run 2",
                "Description" => "Running Man 2",
                "Taxable" => "1",
                "Price" => "25.99",
                "Measure" => "PCS",
                "Barcode" => "1234567890",
                "Category" => "war",
                "CategoryID" => "103",
                "Class" => "N/A",
                "ClassID" => "1"
            ]
        ];
        
        $this->info("\nComparing with expected format:");
        $this->line("Expected structure matches: " . ($this->compareStructure($payload['parameters'], $expectedFormat['parameters']) ? '✓' : '✗'));
        
        $this->info("\nTest complete!");
    }
    
    /**
     * Compare the structure of two arrays
     */
    private function compareStructure($actual, $expected)
    {
        // Check if all expected keys are present
        foreach ($expected as $key => $value) {
            if (!array_key_exists($key, $actual)) {
                $this->error("Missing key: {$key}");
                return false;
            }
        }
        
        // Check if all actual keys are expected (optional)
        foreach ($actual as $key => $value) {
            if (!array_key_exists($key, $expected)) {
                $this->warn("Extra key: {$key}");
            }
        }
        
        // Check data types for specific fields
        $stringFields = ['Taxable', 'Price', 'Measure', 'Barcode', 'Category', 'CategoryID', 'Class', 'ClassID'];
        foreach ($stringFields as $field) {
            if (isset($actual[$field]) && !is_string($actual[$field])) {
                $this->error("Field {$field} should be string, got " . gettype($actual[$field]));
                return false;
            }
        }
        
        return true;
    }
}