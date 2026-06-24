<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Queue;

class ManualNotificationQueueTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:manual-notification-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manual notification queue test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== MANUAL NOTIFICATION QUEUE TEST ===");
        
        // Get a user and book
        $user = User::first();
        $book = Book::first();
        
        if (!$user || !$book) {
            $this->error('Need at least one user and one book to test');
            return;
        }
        
        $this->info("User: " . $user->name);
        $this->info("Book: " . $book->title);
        
        // Create notification
        $notification = new BookSubmitted($book);
        
        $this->info("Notification class: " . get_class($notification));
        $this->info("Implements ShouldQueue: " . (in_array('Illuminate\Contracts\Queue\ShouldQueue', class_implements($notification)) ? 'Yes' : 'No'));
        
        // Try to manually queue the notification
        try {
            $this->info("Attempting to manually queue notification...");
            
            // Get the queue manager
            $queueManager = app('queue');
            $this->info("Queue manager: " . get_class($queueManager));
            
            // Get the default queue connection
            $queueConnection = $queueManager->connection();
            $this->info("Queue connection: " . get_class($queueConnection));
            
            // Try to push the notification job manually
            // This mimics what Laravel does internally
            $jobData = [
                'notification' => $notification,
                'notifiable' => $user,
                'channel' => 'mail'
            ];
            
            $jobId = $queueConnection->push('Illuminate\Notifications\SendQueuedNotifications', $jobData);
            $this->info("Job pushed with ID: " . $jobId);
            
        } catch (\Exception $e) {
            $this->error("Failed to manually queue notification: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
    }
}