<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\TestQueueJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;

class DirectQueueTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:direct-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Direct queue test';

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

        $this->info("Dispatching test job...");
        
        // Dispatch a job directly
        TestQueueJob::dispatch('Direct test message');
        
        $this->info("Test job dispatched. Check if it gets processed by the queue worker.");
    }
}