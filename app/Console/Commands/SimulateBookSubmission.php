<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;

class SimulateBookSubmission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:book-submission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate book submission to test admin notifications';

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
            'title' => 'Test Book for Notification Testing',
            'isbn' => 'TEST-' . time(),
            'genre' => 'Testing',
            'price' => 9.99,
            'book_type' => 'digital',
            'description' => 'This is a test book for notification testing purposes.',
            'status' => 'pending'
        ]);

        $this->info("Using book: " . $book->title . " (ID: " . $book->id . ")");

        // Load the user relationship for the notification
        $book->load('user');

        // Notify the user who submitted the book
        $user->notify(new BookSubmitted($book));
        $this->info("Notification sent to author: " . $user->name);

        // Notify all admins about the new book submission
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        $adminCount = 0;
        foreach ($admins as $admin) {
            $admin->notify(new BookSubmitted($book));
            $this->info("Notification sent to admin: " . $admin->name . " (" . $admin->email . ")");
            $adminCount++;
        }

        $this->info("Sent notifications to " . $adminCount . " admins.");
        $this->info("Check the jobs table to see if notifications were queued.");
    }
}