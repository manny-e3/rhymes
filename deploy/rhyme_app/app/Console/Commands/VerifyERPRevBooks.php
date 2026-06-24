<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\User;

class VerifyERPRevBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-erprev-books';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify ERPRev books were created with correct status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying ERPRev books...');
        
        // Count total books
        $totalBooks = Book::count();
        $pendingBooks = Book::where('status', 'pending')->count();
        $acceptedBooks = Book::where('status', 'accepted')->count();
        $stockedBooks = Book::where('status', 'stocked')->count();
        $rejectedBooks = Book::where('status', 'rejected')->count();
        
        $this->info("Total books: {$totalBooks}");
        $this->info("Pending books: {$pendingBooks}");
        $this->info("Accepted books: {$acceptedBooks}");
        $this->info("Stocked books: {$stockedBooks}");
        $this->info("Rejected books: {$rejectedBooks}");
        
        // Show some sample pending books
        $pendingSample = Book::where('status', 'pending')->limit(5)->get();
        if ($pendingSample->count() > 0) {
            $this->info("\nSample pending books:");
            foreach ($pendingSample as $book) {
                $this->line("- {$book->title} (Genre: {$book->genre}, Author: {$book->user->name})");
            }
        }
        
        // Show genre distribution
        $genres = Book::select('genre')->distinct()->pluck('genre');
        $this->info("\nUnique genres found: " . $genres->count());
        
        // Show some sample genres
        $sampleGenres = $genres->take(10);
        $this->info("Sample genres:");
        foreach ($sampleGenres as $genre) {
            $count = Book::where('genre', $genre)->count();
            $this->line("- {$genre}: {$count} books");
        }
        
        $this->info("\nVerification complete!");
    }
}