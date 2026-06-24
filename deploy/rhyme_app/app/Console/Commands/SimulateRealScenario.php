<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;
use App\Notifications\BookStatusChanged;

class SimulateRealScenario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:real-scenario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate the real scenario described by the user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== SIMULATING REAL SCENARIO ===");
        
        // Step 1: Simulate author submitting a book
        $this->info("\n1. Simulating author submitting a book...");
        
        // Get an author (non-admin user)
        $author = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$author) {
            // Create a test author if none exists
            $author = User::factory()->create([
                'name' => 'Test Author',
                'email' => 'test.author@example.com'
            ]);
        }
        
        $this->info("Author: " . $author->name . " (" . $author->email . ")");
        
        // Create a test book
        $book = Book::create([
            'user_id' => $author->id,
            'title' => 'Test Book for Real Scenario',
            'isbn' => 'REAL-' . time(),
            'genre' => 'Testing',
            'price' => 19.99,
            'book_type' => 'digital',
            'description' => 'This is a test book for the real scenario.',
            'status' => 'pending'
        ]);
        
        $this->info("Book created: " . $book->title);
        
        // Load the user relationship
        $book->load('user');
        
        // Send BookSubmitted notification to author
        $this->info("Sending BookSubmitted notification to author...");
        $startTime = microtime(true);
        $author->notify(new BookSubmitted($book));
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000;
        $this->info("Author notification sent in: " . number_format($duration, 2) . " ms");
        
        // Send BookSubmitted notification to admins
        $this->info("Sending BookSubmitted notification to admins...");
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        $adminCount = 0;
        foreach ($admins as $admin) {
            $startTime = microtime(true);
            $admin->notify(new BookSubmitted($book));
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000;
            $this->info("Admin notification sent to " . $admin->name . " in: " . number_format($duration, 2) . " ms");
            $adminCount++;
        }
        
        $this->info("Sent notifications to " . $adminCount . " admins.");
        
        // Step 2: Simulate admin approving the book
        $this->info("\n2. Simulating admin approving the book...");
        
        if ($admins->count() > 0) {
            $admin = $admins->first();
            $this->info("Admin: " . $admin->name . " (" . $admin->email . ")");
            
            // Send BookStatusChanged notification to author
            $this->info("Sending BookStatusChanged notification to author...");
            $startTime = microtime(true);
            $author->notify(new BookStatusChanged($book, 'pending', 'accepted'));
            $endTime = microtime(true);
            $duration = ($endTime - $startTime) * 1000;
            $this->info("Status change notification sent in: " . number_format($duration, 2) . " ms");
        } else {
            $this->warn("No admins found to simulate approval.");
        }
        
        $this->info("\n=== SIMULATION COMPLETE ===");
        $this->info("Check email accounts to see if notifications were received.");
    }
}