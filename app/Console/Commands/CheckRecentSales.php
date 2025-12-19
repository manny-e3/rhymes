<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncRevSalesJob;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class CheckRecentSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:check-sales {--book-id=} {--days=1} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check recent sales for author books from ERPREV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bookId = $this->option('book-id');
        $days = $this->option('days');
        $all = $this->option('all');
        
        if ($all) {
            $this->info('Checking sales for all books...');
            // Dispatch a single job to check all books
            SyncRevSalesJob::dispatch($days);
            $this->info('Sales check job dispatched for all books');
        } elseif ($bookId) {
            $this->info("Checking sales for book ID: {$bookId}");
            // Dispatch a job to check sales for a specific book
            $book = Book::find($bookId);
            if (!$book) {
                $this->error("Book with ID {$bookId} not found");
                return 1;
            }
            
            if (!$book->rev_book_id) {
                $this->error("Book with ID {$bookId} is not registered in ERPREV");
                return 1;
            }
            
            SyncRevSalesJob::dispatch($days, $bookId);
            $this->info("Sales check job dispatched for book ID: {$bookId}");
        } else {
            $this->info('Checking recent sales for all books...');
            // Default behavior - check recent sales for all books
            SyncRevSalesJob::dispatch($days);
            $this->info('Sales check job dispatched for recent sales');
        }
        
        return 0;
    }
}