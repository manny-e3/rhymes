<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Queue;

class ManualNotificationTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:manual-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manual notification test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get a user and book
        $user = User::first();
        $book = Book::first();

        if (!$user || !$book) {
            $this->error('Need at least one user and one book to test');
            return;
        }

        $this->info("Creating notification...");
        
        // Create the notification
        $notification = new BookSubmitted($book);
        
        $this->info("Notification class: " . get_class($notification));
        $this->info("Implements ShouldQueue: " . (in_array('Illuminate\Contracts\Queue\ShouldQueue', class_implements($notification)) ? 'Yes' : 'No'));
        
        $this->info("Sending notification...");
        
        // Send the notification
        $startTime = microtime(true);
        $user->notify($notification);
        $endTime = microtime(true);
        
        $duration = ($endTime - $startTime) * 1000;
        $this->info("Notification sent in: " . number_format($duration, 2) . " ms");
    }
}