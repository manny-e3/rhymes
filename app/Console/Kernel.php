<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncRevSales::class,
        Commands\SyncRevInventory::class,
        Commands\RegisterWebhooks::class,
        Commands\TestWebhook::class,
        Commands\DispatchSyncJobs::class,
        Commands\TestQueueJob::class,
        Commands\CheckRecentSales::class,
        Commands\DispatchPeriodicSync::class,
        Commands\ScheduledInventorySync::class,
        Commands\TestErpRevConnection::class,
        Commands\RegisterBookInErprev::class,
        Commands\TestBookRegistration::class,
        Commands\UpdateRevSyncLogsEnum::class,
        Commands\TestErpRevData::class,
        Commands\ShowBookReviewLogs::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

         $schedule->command('queue:work --tries=3 --timeout=90')
             ->everyMinute()
             ->withoutOverlapping()
             ->runInBackground();

             
        // Run the inventory sync with sales value processing every minute
        $schedule->command('rev:sync-inventory --process-sales-value')->everyMinute();
        
  
            $schedule->command('rev:sync-inventory --process-sales-value')->everyMinute();

        //$schedule->command('rev:periodic-sync --type=sales --days=1')->everyMinute();
        
        // Dispatch inventory sync job every hour
        $schedule->command('rev:periodic-sync --type=inventory')->hourly();
        
        // Dispatch both jobs daily at 2 AM for comprehensive sync
        $schedule->command('rev:periodic-sync --type=both --days=1')->dailyAt('02:00');
        
        // Run inventory sync with sales value processing daily at 3 AM
        $schedule->command('rev:scheduled-inventory-sync')->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}