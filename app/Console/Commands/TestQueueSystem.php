<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobFailed;
use App\Jobs\TestQueueJob;

class TestQueueSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:queue-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the queue system';

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

        Queue::failing(function (JobFailed $event) {
            $this->error('Job failed: ' . get_class($event->job));
            $this->error('Exception: ' . $event->exception->getMessage());
        });

        // Try to dispatch a simple job
        TestQueueJob::dispatch('Test message');

        $this->info("Test job pushed to queue. Check if it gets processed.");
    }
}