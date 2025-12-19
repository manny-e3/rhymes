<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowBookReviewLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:logs {--lines=50 : Number of lines to show} {--filter= : Filter logs by keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show recent book review logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            $this->error('Log file not found: ' . $logFile);
            return 1;
        }
        
        $lines = $this->option('lines');
        $filter = $this->option('filter');
        
        // Get the last N lines from the log file
        $logContent = shell_exec("tail -n {$lines} " . escapeshellarg($logFile));
        
        if ($filter) {
            // Filter the logs
            $filteredLines = [];
            $linesArray = explode("\n", $logContent);
            
            foreach ($linesArray as $line) {
                if (stripos($line, $filter) !== false) {
                    $filteredLines[] = $line;
                }
            }
            
            $logContent = implode("\n", $filteredLines);
        }
        
        $this->line($logContent);
        
        return 0;
    }
}