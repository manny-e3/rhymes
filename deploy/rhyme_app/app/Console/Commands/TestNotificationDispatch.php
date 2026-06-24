<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;

class TestNotificationDispatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification dispatch behavior';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Listen for queue events
        Queue::before(function (JobProcessing $event) {
            $this->info('Job processing: ' . get_class($event->job));
        });

        Queue::after(function (JobProcessed $event) {
            $this->info('Job processed: ' . get_class($event->job));
        });

        // Get a user and book
        $user = User::first();
        $book = Book::first();

        if (!$user || !$book) {
            $this->error('Need at least one user and one book to test');
            return;
        }

        $this->info("Testing notification dispatch...");
        $this->info("User: " . $user->name);
        $this->info("Book: " . $book->title);

        // Record time before dispatch
        $startTime = microtime(true);
        $this->info("Dispatch time: " . date('Y-m-d H:i:s.u'));

        // Dispatch the notification
        $user->notify(new BookSubmitted($book));

        // Record time after dispatch
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $this->info("After dispatch time: " . date('Y-m-d H:i:s.u'));
        $this->info("Dispatch duration: " . number_format($duration, 2) . " ms");

        if ($duration < 10) {
            $this->info("Notification was likely queued (fast dispatch)");
        } else {
            $this->info("Notification was likely processed synchronously (slow dispatch)");
        }
    }
}