<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateRevSyncLogsEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:update-enum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update rev_sync_logs area enum to include products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Update the enum values
            DB::statement("ALTER TABLE rev_sync_logs MODIFY COLUMN area ENUM('books', 'sales', 'inventory', 'products')");
            $this->info('Successfully updated the area enum values in rev_sync_logs table.');
        } catch (\Exception $e) {
            $this->error('Error updating enum values: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}