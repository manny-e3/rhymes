<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncRevSalesJob;
use App\Jobs\SyncRevInventoryJob;

class DispatchPeriodicSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:periodic-sync {--type=sales} {--days=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periodically dispatch sync jobs to check for updates';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->option('type');
        $days = $this->option('days');
        
        switch ($type) {
            case 'sales':
                SyncRevSalesJob::dispatch($days);
                $this->info('Sales sync job dispatched to queue');
                break;
                
            case 'inventory':
                SyncRevInventoryJob::dispatch();
                $this->info('Inventory sync job dispatched to queue');
                break;
                
            case 'both':
                SyncRevSalesJob::dispatch($days);
                SyncRevInventoryJob::dispatch();
                $this->info('Both sales and inventory sync jobs dispatched to queue');
                break;
                
            default:
                $this->error('Invalid sync type. Use "sales", "inventory", or "both"');
                return 1;
        }
        
        return 0;
    }
}