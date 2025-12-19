<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;

class TestAdminNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test admin notification for book submission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing admin notification for book submission...');
        
        // Get an author user
        $author = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$author) {
            $this->error('No author user found');
            return;
        }
        
        $this->info('Author: ' . $author->name . ' (' . $author->email . ')');
        
        // Get or create a test book
        $book = Book::firstOrCreate([
            'user_id' => $author->id,
            'title' => 'Test Book for Admin Notification',
            'isbn' => 'TEST-' . time(),
            'genre' => 'Testing',
            'price' => 9.99,
            'book_type' => 'digital',
            'description' => 'This is a test book to verify admin notifications.',
            'status' => 'pending'
        ]);
        
        $this->info('Book: ' . $book->title);
        
        // Load the user relationship
        $book->load('user');
        
        // Get all admins
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        $this->info('Found ' . $admins->count() . ' admin(s)');
        
        if ($admins->isEmpty()) {
            $this->error('No admins found! Cannot send notification.');
            return;
        }
        
        // Send notification to each admin
        foreach ($admins as $admin) {
            $this->info('Sending notification to: ' . $admin->name . ' (' . $admin->email . ')');
            
            try {
                $admin->notify(new BookSubmitted($book));
                $this->info('✓ Notification sent successfully to ' . $admin->name);
            } catch (\Exception $e) {
                $this->error('✗ Failed to send notification to ' . $admin->name . ': ' . $e->getMessage());
            }
        }
        
        $this->info('Test completed. Check admin email inbox for notifications.');
    }
}