<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;

class TestBookNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:book-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test book submission notification';

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
        
        // Create a test book
        $book = Book::first();
        if (!$book) {
            $this->error('No book found');
            return;
        }
        
        try {
            // Notify the user who submitted the book
            $user->notify(new BookSubmitted($book));
            
            // Notify all admins about the new book submission
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new BookSubmitted($book));
            }
            
            $this->info("Book submission notifications sent successfully!");
        } catch (\Exception $e) {
            $this->error("Failed to send book notification: " . $e->getMessage());
        }
    }
}