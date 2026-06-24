<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class DetailedConfigCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:detailed-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detailed configuration check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== QUEUE CONFIGURATION ===');
        $this->info('Default queue connection: ' . Config::get('queue.default'));
        $this->info('Queue connections:');
        $connections = Config::get('queue.connections');
        foreach ($connections as $name => $config) {
            $this->line("  {$name}: " . json_encode($config));
        }
        
        $this->info("\n=== MAIL CONFIGURATION ===");
        $this->info('Default mail driver: ' . Config::get('mail.default'));
        $this->info('Mail mailers:');
        $mailers = Config::get('mail.mailers');
        foreach ($mailers as $name => $config) {
            $this->line("  {$name}: " . json_encode($config));
        }
        
        $this->info("\n=== DATABASE CONFIGURATION ===");
        $this->info('Default database connection: ' . Config::get('database.default'));
        $this->info('Database connections:');
        $dbConnections = Config::get('database.connections');
        foreach ($dbConnections as $name => $config) {
            $host = $config['host'] ?? 'N/A';
            $database = $config['database'] ?? 'N/A';
            $this->line("  {$name}: host={$host}, database={$database}");
        }
    }
}