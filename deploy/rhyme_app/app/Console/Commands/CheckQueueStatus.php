<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckQueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check queue status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            $this->info("Queue Status:");
            $this->line("Pending jobs: " . $pendingJobs);
            $this->line("Failed jobs: " . $failedJobs);
            
            if ($pendingJobs > 0) {
                $this->info("Processing pending jobs...");
                // Process one job to test
                $this->call('queue:work', [
                    '--max-jobs' => 1,
                    '--once' => true
                ]);
            }
        } catch (\Exception $e) {
            $this->error("Error checking queue status: " . $e->getMessage());
        }
    }
}