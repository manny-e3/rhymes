<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Queue;

class TestNotificationQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if notifications are properly queued';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification queue system...');
        
        // Get a user and book
        $user = User::first();
        $book = Book::first();
        
        if (!$user || !$book) {
            $this->error('Need at least one user and one book to test');
            return;
        }
        
        $this->info('User: ' . $user->name);
        $this->info('Book: ' . $book->title);
        
        // Check queue configuration
        $this->info('Queue configuration:');
        $this->info('  Default connection: ' . config('queue.default'));
        $this->info('  Mail driver: ' . config('mail.default'));
        
        // Create notification
        $notification = new BookSubmitted($book);
        
        $this->info('Notification class: ' . get_class($notification));
        $this->info('Implements ShouldQueue: ' . (in_array('Illuminate\Contracts\Queue\ShouldQueue', class_implements($notification)) ? 'Yes' : 'No'));
        
        // Test notification dispatch timing
        $this->info('Testing notification dispatch timing...');
        $startTime = microtime(true);
        $user->notify($notification);
        $endTime = microtime(true);
        
        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $this->info('Notification dispatch took: ' . number_format($duration, 2) . ' ms');
        
        if ($duration < 50) {
            $this->info('✓ Notification was likely queued (fast dispatch)');
        } else {
            $this->warn('⚠ Notification was likely processed synchronously (slow dispatch)');
            $this->warn('This may indicate an issue with the queue system');
        }
        
        $this->info('Test completed.');
    }
}