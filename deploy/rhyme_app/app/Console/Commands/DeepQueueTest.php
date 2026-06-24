<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use App\Jobs\TestQueueJob;

class DeepQueueTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:deep-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deep queue system test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== DEEP QUEUE TEST ===");
        
        // Check configuration
        $this->info("1. Configuration Check:");
        $this->info("   QUEUE_CONNECTION: " . env('QUEUE_CONNECTION'));
        $this->info("   config('queue.default'): " . Config::get('queue.default'));
        $this->info("   Actual queue connection: " . Queue::getDefaultDriver());
        
        // Check if we're using sync driver
        $isSync = (Queue::getDefaultDriver() === 'sync');
        $this->info("   Is using sync driver: " . ($isSync ? 'YES' : 'NO'));
        
        if ($isSync) {
            $this->warn("   WARNING: Jobs will be executed immediately, not queued!");
        }
        
        // Try to force database driver
        $this->info("\n2. Testing with explicit database driver:");
        try {
            Queue::connection('database')->push(new class {
                public function fire($job, $data) {
                    echo "Direct database job executed!\n";
                    $job->delete();
                }
            }, ['test' => 'data']);
            
            $this->info("   Job pushed to database queue successfully");
        } catch (\Exception $e) {
            $this->error("   Failed to push job: " . $e->getMessage());
        }
        
        // Test job dispatching
        $this->info("\n3. Testing job dispatching:");
        $startTime = microtime(true);
        TestQueueJob::dispatch('test message');
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000;
        $this->info("   Job dispatched in: " . number_format($duration, 2) . " ms");
        
        if ($duration < 50) {
            $this->info("   Job was likely queued (fast dispatch)");
        } else {
            $this->info("   Job was likely executed synchronously (slow dispatch)");
        }
    }
}