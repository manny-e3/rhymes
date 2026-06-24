<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;
use App\Models\User;

class TestBookRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:test-book-registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test book registration with ERPREV';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Testing book registration with ERPREV...');
        
        // Check if we have any accepted books
        $book = Book::where('status', 'accepted')->first();
        
        if (!$book) {
            // Create a test user if none exists
            $user = User::first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Test Author',
                    'email' => 'test@author.com',
                    'password' => bcrypt('password'),
                ]);
            }
            
            // Create a test book
            $book = Book::create([
                'user_id' => $user->id,
                'isbn' => '978-3-16-148410-0',
                'title' => 'Test Book for ERPREV Integration',
                'genre' => 'Fiction',
                'price' => 29.99,
                'book_type' => 'physical',
                'description' => 'This is a test book created to verify ERPREV integration.',
                'status' => 'accepted',
            ]);
            
            $this->info("Created test book: {$book->title}");
        } else {
            $this->info("Using existing book: {$book->title}");
        }
        
        // Attempt to register the book with ERPREV
        $this->info("Registering book with ERPREV...");
        $result = $revService->registerProduct($book);
        
        if ($result['success']) {
            $this->info('✓ Book registered successfully!');
            $this->line("Product ID: {$result['product_id']}");
            $this->line("Message: {$result['message']}");
            
            // Update the book with the ERPREV product ID
            $book->update(['rev_book_id' => $result['product_id']]);
            $this->info('Book updated with ERPREV product ID');
        } else {
            $this->error('✗ Book registration failed!');
            $this->line("Message: {$result['message']}");
        }
        
        return 0;
    }
}