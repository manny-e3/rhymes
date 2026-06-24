<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncRevSalesJob;
use App\Jobs\SyncRevInventoryJob;

class TestQueueJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:test-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test queue job dispatching';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Dispatching test jobs...');
        
        // Dispatch a sales sync job
        $salesJob = new SyncRevSalesJob(1);
        dispatch($salesJob);
        $this->info('Sales sync job dispatched');
        
        // Dispatch an inventory sync job
        $inventoryJob = new SyncRevInventoryJob();
        dispatch($inventoryJob);
        $this->info('Inventory sync job dispatched');
        
        $this->info('Test jobs dispatched successfully!');
        
        return 0;
    }
}