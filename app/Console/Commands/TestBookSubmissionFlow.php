<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;

class TestBookSubmissionFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:book-submission-flow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete book submission flow including admin notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing complete book submission flow...');
        
        // Get an author user
        $author = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$author) {
            $this->error('No author user found');
            return;
        }
        
        $this->info('Author: ' . $author->name . ' (' . $author->email . ')');
        
        // Create a test book (simulating the book creation process)
        $bookData = [
            'title' => 'Test Book for Flow Testing',
            'isbn' => 'FLOW-' . time(),
            'genre' => 'Testing',
            'price' => 14.99,
            'book_type' => 'digital',
            'description' => 'This is a test book to verify the complete book submission flow.',
            'status' => 'pending'
        ];
        
        // Simulate the book creation process
        $book = Book::create(array_merge($bookData, ['user_id' => $author->id]));
        
        $this->info('Book created: ' . $book->title . ' (ID: ' . $book->id . ')');
        
        // Load the user relationship
        $book->load('user');
        
        // Simulate notifying the author
        $this->info('Notifying author...');
        try {
            $author->notify(new BookSubmitted($book));
            $this->info('✓ Author notification sent successfully');
        } catch (\Exception $e) {
            $this->error('✗ Failed to send author notification: ' . $e->getMessage());
        }
        
        // Simulate notifying admins (this is the part we want to ensure works)
        $this->info('Notifying admins...');
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        $this->info('Found ' . $admins->count() . ' admin(s)');
        
        if ($admins->isEmpty()) {
            $this->error('No admins found!');
            return;
        }
        
        foreach ($admins as $admin) {
            $this->info('Sending notification to: ' . $admin->name . ' (' . $admin->email . ')');
            
            try {
                $admin->notify(new BookSubmitted($book));
                $this->info('✓ Admin notification sent successfully to ' . $admin->name);
            } catch (\Exception $e) {
                $this->error('✗ Failed to send admin notification to ' . $admin->name . ': ' . $e->getMessage());
            }
        }
        
        $this->info('Complete book submission flow test finished.');
        $this->info('Check admin email inbox for notifications.');
    }
}