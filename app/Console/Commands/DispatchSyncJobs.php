<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SyncRevSalesJob;
use App\Jobs\SyncRevInventoryJob;

class DispatchSyncJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:dispatch-sync {--type=sales} {--book-id=} {--days=1} {--process-sales-value} {--notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch sync jobs to the queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->option('type');
        $bookId = $this->option('book-id');
        $days = $this->option('days');
        $processSalesValue = $this->option('process-sales-value');
        $notify = $this->option('notify');
        
        switch ($type) {
            case 'sales':
                SyncRevSalesJob::dispatch($days, $bookId);
                $this->info('Sales sync job dispatched to queue');
                break;
                
            case 'inventory':
                SyncRevInventoryJob::dispatch($bookId, $processSalesValue, $notify);
                $this->info('Inventory sync job dispatched to queue');
                break;
                
            case 'both':
                SyncRevSalesJob::dispatch($days, $bookId);
                SyncRevInventoryJob::dispatch($bookId, $processSalesValue, $notify);
                $this->info('Both sales and inventory sync jobs dispatched to queue');
                break;
                
            default:
                $this->error('Invalid sync type. Use "sales", "inventory", or "both"');
                return 1;
        }
        
        return 0;
    }
}