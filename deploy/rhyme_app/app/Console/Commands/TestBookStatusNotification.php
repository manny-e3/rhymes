<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookStatusChanged;

class TestBookStatusNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:book-status-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test book status change notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get a test user
        $user = User::first();
        if (!$user) {
            $this->error('No user found');
            return;
        }
        
        // Get a test book
        $book = Book::first();
        if (!$book) {
            $this->error('No book found');
            return;
        }
        
        try {
            // Notify the user about status change
            $user->notify(new BookStatusChanged($book, 'pending', 'accepted'));
            
            $this->info("Book status change notification sent successfully!");
        } catch (\Exception $e) {
            $this->error("Failed to send book status notification: " . $e->getMessage());
        }
    }
}