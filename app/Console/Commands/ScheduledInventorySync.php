<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncRevInventoryJob;

class ScheduledInventorySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:scheduled-inventory-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scheduled inventory sync with sales value processing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Dispatching scheduled inventory sync job with sales value processing...');
        
        // Dispatch the inventory sync job with sales value processing and notifications
        SyncRevInventoryJob::dispatch(null, true, true);
        
        $this->info('Scheduled inventory sync job dispatched successfully!');
        
        return 0;
    }
}