<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use App\Jobs\TestQueueJob;

class DirectDatabaseQueueTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:direct-db-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Direct database queue test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== DIRECT DATABASE QUEUE TEST ===");
        
        try {
            // Get the database queue connection directly
            $queue = Queue::connection('database');
            
            $this->info("Queue connection class: " . get_class($queue));
            
            // Try to push a proper job
            $jobId = $queue->push(TestQueueJob::class, ['message' => 'Direct test']);
            
            $this->info("Job pushed with ID: " . $jobId);
            
            // Check if job exists
            $this->info("Checking database for jobs...");
            $this->call('db_check');
        } catch (\Exception $e) {
            $this->error("Failed: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
    }
}