<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Log;

class DebugNotificationQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:notification-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug notification queue behavior';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get an author user (not admin)
        $user = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$user) {
            $this->error('No author user found');
            return;
        }

        $this->info("Using user: " . $user->name . " (" . $user->email . ")");

        // Get or create a test book for this user
        $book = Book::firstOrCreate([
            'user_id' => $user->id,
            'title' => 'Debug Test Book',
            'isbn' => 'DEBUG-' . time(),
            'genre' => 'Debugging',
            'price' => 1.99,
            'book_type' => 'digital',
            'description' => 'This is a debug test book.',
            'status' => 'pending'
        ]);

        $this->info("Using book: " . $book->title . " (ID: " . $book->id . ")");

        // Load the user relationship for the notification
        $book->load('user');

        // Log before sending notifications
        Log::info('Debug: About to send notifications', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'timestamp' => now()->toISOString()
        ]);

        $this->info("Sending notification at: " . now()->toISOString());

        // Notify the user who submitted the book
        $user->notify(new BookSubmitted($book));
        $this->info("Notification sent to author: " . $user->name . " at: " . now()->toISOString());

        // Notify all admins about the new book submission
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        $adminCount = 0;
        foreach ($admins as $admin) {
            $admin->notify(new BookSubmitted($book));
            $this->info("Notification sent to admin: " . $admin->name . " (" . $admin->email . ") at: " . now()->toISOString());
            $adminCount++;
        }

        $this->info("Sent notifications to " . $adminCount . " admins.");
        $this->info("Check the laravel.log file to see timing information.");
    }
}