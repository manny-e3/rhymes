<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;

class TestUserCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-user-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the ERPREV categories function in user context';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing ERPREV categories function in user context...');
        
        $revService = new RevService();
        $result = $revService->getItemCategories();
        
        if ($result['success']) {
            $this->info('Categories retrieved successfully!');
            $this->info('Count: ' . count($result['data']['records'] ?? []));
            
            // Show first few categories as example
            $records = $result['data']['records'] ?? [];
            if (!empty($records)) {
                $this->info('First 5 categories:');
                foreach (array_slice($records, 0, 5) as $category) {
                    $this->line('- ' . ($category['Name'] ?? $category['name'] ?? 'Unknown'));
                }
            }
        } else {
            $this->error('Failed to retrieve categories: ' . $result['message']);
        }
    }
}