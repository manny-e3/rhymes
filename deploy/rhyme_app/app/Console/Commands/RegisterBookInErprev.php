<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;
use App\Models\Book;

class RegisterBookInErprev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:register-book {book_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a book in ERPREV';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $bookId = $this->argument('book_id');
        
        $this->info("Looking for book with ID: {$bookId}");
        
        $book = Book::find($bookId);
        
        if (!$book) {
            $this->error("Book with ID {$bookId} not found!");
            return 1;
        }
        
        $this->info("Found book: {$book->title}");
        
        if ($book->status !== 'accepted') {
            $this->error("Book must be accepted before registering in ERPREV!");
            return 1;
        }
        
        if ($book->rev_book_id) {
            $this->info("Book is already registered in ERPREV with ID: {$book->rev_book_id}");
            return 0;
        }
        
        $this->info("Registering book in ERPREV...");
        
        $result = $revService->registerProduct($book);
        
        if ($result['success']) {
            $book->update(['rev_book_id' => $result['product_id']]);
            $this->info("✓ Book registered successfully in ERPREV!");
            $this->line("ERPREV Product ID: {$result['product_id']}");
        } else {
            $this->error("✗ Failed to register book in ERPREV!");
            $this->line("Error: {$result['message']}");
            return 1;
        }
        
        return 0;
    }
}