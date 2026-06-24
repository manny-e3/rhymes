<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitorLog;

class FillVisitorLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor-logs:fill-location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill missing locations in the visitor_logs table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding visitor logs without a location...');

        // Get visitors with no location and a valid IP
        $logs = VisitorLog::whereNull('location')
            ->whereNotNull('ip_address')
            ->where('ip_address', '!=', '0.0.0.0')
            ->get();

        if ($logs->isEmpty()) {
            $this->info('No logs found that require location updating.');
            return;
        }

        // Filter out private IPs that are guaranteed to fail
        $validLogs = $logs->filter(function($log) {
            $ip = $log->ip_address;
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
        });

        if ($validLogs->isEmpty()) {
            $this->warn('Found ' . $logs->count() . ' logs without location, but all have private/local IP addresses.');
            $this->line('Local IP addresses (like 127.0.0.1) cannot be geolocated using the public API.');
            return;
        }

        $this->info('Found ' . $validLogs->count() . ' logs with public IP to update. Processing...');

        $bar = $this->output->createProgressBar($validLogs->count());
        $bar->start();

        $successCount = 0;

        foreach ($validLogs as $log) {
            $url = "http://ip-api.com/php/" . $log->ip_address;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response !== false) {
                $data = @unserialize($response);
                if (is_array($data) && $data['status'] === 'success') {
                    $location = $data['city'] . ", " . $data['regionName'] . ", " . $data['country'];
                    $log->update(['location' => $location]);
                    $successCount++;
                }
            }

            // Sleep 1.5 seconds between requests (ip-api.com allows 45 requests per minute for free tier)
            usleep(1500000); 

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Completed. Successfully updated location for {$successCount} records.");
    }
}
