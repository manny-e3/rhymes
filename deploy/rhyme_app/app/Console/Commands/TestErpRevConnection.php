<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;

class TestErpRevConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the connection to the ERPREV API';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Testing ERPREV API connection...');
        
        // Let's also output the configuration for debugging
        $this->line('ERPREV Account URL: ' . config('services.erprev.account_url'));
        $this->line('ERPREV API Key: ' . (config('services.erprev.api_key') ? 'SET' : 'NOT SET'));
        $this->line('ERPREV API Secret: ' . (config('services.erprev.api_secret') ? 'SET' : 'NOT SET'));
        $this->line('ERPREV Sync Enabled: ' . (config('services.erprev.enabled') ? 'YES' : 'NO'));
        
        $result = $revService->testConnection();
        
        if ($result['success']) {
            $this->info('✓ Connection successful!');
            $this->line("Status Code: {$result['status_code']}");
            $this->line("Message: {$result['message']}");
        } else {
            $this->error('✗ Connection failed!');
            $this->line("Message: {$result['message']}");
        }
        
        return 0;
    }
}