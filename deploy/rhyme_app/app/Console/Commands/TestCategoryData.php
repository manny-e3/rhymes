<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;

class TestCategoryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-category-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the ERPREV categories function to verify name and ID data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing ERPREV categories function...');
        
        $revService = new RevService();
        $result = $revService->getItemCategories();
        
        if ($result['success']) {
            $this->info('Categories retrieved successfully!');
            $this->info('Total categories: ' . count($result['categories'] ?? []));
            
            // Show first few categories as example
            $categories = $result['categories'] ?? [];
            if (!empty($categories)) {
                $this->info('First 5 categories with ID and Name:');
                foreach (array_slice($categories, 0, 5) as $category) {
                    if (is_array($category)) {
                        $this->line('- ID: ' . ($category['id'] ?? 'N/A') . ', Name: ' . ($category['name'] ?? 'Unknown'));
                    } else {
                        $this->line('- ID: N/A, Name: ' . $category);
                    }
                }
            }
            
            // Check if we have both ID and Name data
            $hasIds = false;
            $hasNames = false;
            foreach ($categories as $category) {
                if (is_array($category)) {
                    if (!empty($category['id'])) {
                        $hasIds = true;
                    }
                    if (!empty($category['name'])) {
                        $hasNames = true;
                    }
                }
            }
            
            $this->info('');
            $this->info('Data validation:');
            $this->info('- Categories with IDs: ' . ($hasIds ? 'Yes' : 'No'));
            $this->info('- Categories with Names: ' . ($hasNames ? 'Yes' : 'No'));
        } else {
            $this->error('Failed to retrieve categories: ' . $result['message']);
        }
    }
}