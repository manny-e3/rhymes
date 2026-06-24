<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevService;

class TestErpRevData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rev:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test ERPREV data retrieval';

    /**
     * Execute the console command.
     */
    public function handle(RevService $revService)
    {
        $this->info('Testing ERPREV data retrieval...');
        
        // Test connection first
        $this->info('1. Testing connection...');
        $connectionResult = $revService->testConnection();
        
        if (!$connectionResult['success']) {
            $this->error('Connection failed: ' . $connectionResult['message']);
            if (isset($connectionResult['data'])) {
                $this->line('Response data: ' . json_encode($connectionResult['data'], JSON_PRETTY_PRINT));
            }
            return 1;
        }
        
        $this->info('✓ Connection successful');
        if (isset($connectionResult['data'])) {
            $this->line('Response: ' . json_encode($connectionResult['data'], JSON_PRETTY_PRINT));
        }
        
        // Test getting a small set of products
        $this->info('2. Testing product list retrieval...');
        $productsResult = $revService->getProductsList(['limit' => 1]);
        
        if (!$productsResult['success']) {
            $this->error('Product list retrieval failed: ' . $productsResult['message']);
            return 1;
        }
        
        $products = $productsResult['data']['records'] ?? [];
        $this->info('✓ Product list retrieved successfully');
        $this->line('  Found ' . count($products) . ' products');
        
        if (count($products) > 0) {
            $this->line('  First product: ' . ($products[0]['Name'] ?? 'N/A'));
        } else {
            $this->warn('  No products found in response');
            $this->line('  Full response: ' . json_encode($productsResult['data'], JSON_PRETTY_PRINT));
        }
        
        // Test getting inventory data
        $this->info('3. Testing inventory data retrieval...');
        $inventoryResult = $revService->getStockList(['limit' => 1]);
        
        if (!$inventoryResult['success']) {
            $this->error('Inventory data retrieval failed: ' . $inventoryResult['message']);
            return 1;
        }
        
        $inventory = $inventoryResult['data']['records'] ?? [];
        $this->info('✓ Inventory data retrieved successfully');
        $this->line('  Found ' . count($inventory) . ' inventory items');
        
        if (count($inventory) > 0) {
            $this->line('  First inventory item: ' . ($inventory[0]['Name'] ?? 'N/A'));
        } else {
            $this->warn('  No inventory items found in response');
            $this->line('  Full response: ' . json_encode($inventoryResult['data'], JSON_PRETTY_PRINT));
        }
        
        // Test getting sales data
        $this->info('4. Testing sales data retrieval...');
        $salesResult = $revService->getSalesItems(['limit' => 1]);
        
        if (!$salesResult['success']) {
            $this->error('Sales data retrieval failed: ' . $salesResult['message']);
            return 1;
        }
        
        $sales = $salesResult['data']['records'] ?? [];
        $this->info('✓ Sales data retrieved successfully');
        $this->line('  Found ' . count($sales) . ' sales records');
        
        if (count($sales) > 0) {
            $this->line('  First sales item: ' . ($sales[0]['Name'] ?? 'N/A'));
        } else {
            $this->warn('  No sales records found in response');
            $this->line('  Full response: ' . json_encode($salesResult['data'], JSON_PRETTY_PRINT));
        }
        
        $this->info('All tests completed successfully!');
        
        return 0;
    }
}