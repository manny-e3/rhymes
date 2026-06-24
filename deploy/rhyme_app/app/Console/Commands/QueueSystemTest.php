<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Bus;

class QueueSystemTest extends Command
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
    protected $description = 'Test the queue system directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing queue system...");
        
        // Test 1: Check if we can dispatch a job
        $this->info("Test 1: Dispatching a simple job...");
        
        // Create a simple closure job
        $job = function() {
            echo "Job executed successfully!\n";
        };
        
        try {
            // Try to dispatch the job
            Bus::dispatch($job);
            $this->info("Job dispatched successfully");
        } catch (\Exception $e) {
            $this->error("Failed to dispatch job: " . $e->getMessage());
        }
        
        // Test 2: Check queue configuration
        $this->info("Test 2: Checking queue configuration...");
        $this->info("Default queue connection: " . config('queue.default'));
        
        // Test 3: Try to push a job directly to the queue
        $this->info("Test 3: Pushing job directly to queue...");
        try {
            Queue::push(new class {
                public function fire($job, $data) {
                    echo "Direct job executed!\n";
                    $job->delete();
                }
            });
            $this->info("Direct job pushed successfully");
        } catch (\Exception $e) {
            $this->error("Failed to push direct job: " . $e->getMessage());
        }
    }
}