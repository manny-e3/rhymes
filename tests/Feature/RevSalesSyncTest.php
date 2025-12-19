<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;

class RevSalesSyncTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that sales sync processes books ensuring they exist in ERP inventory
     *
     * @return void
     */
    public function test_sales_sync_processes_books_with_inventory_verification()
    {
        // Create a user and book with ISBN
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9780241217931',
            'title' => '#Girlboss PB'
        ]);

        // Mock the RevService
        $mockRevService = Mockery::mock('App\Services\RevService');
        $this->app->instance('App\Services\RevService', $mockRevService);
        
        // Mock getSalesItems to return a sale record with barcode
        $mockRevService->shouldReceive('getSalesItems')
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'Barcode' => '9780241217931',
                            'SID' => 'SALE001',
                            'quantity_sold' => 2,
                            'SellingPrice' => 5000,
                            'total_amount' => 10000
                        ]
                    ]
                ]
            ]);
            
        // Mock getStockList to return inventory data with UnitCostPrice
        $mockRevService->shouldReceive('getStockList')
            ->with([])
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'Product' => '#Girlboss PB',
                            'UnitCostPrice' => '8,835.25',
                            'UnitsInStock' => 5
                        ]
                    ]
                ]
            ]);

        // Call the sync sales endpoint
        $response = $this->getJson('/api/rev/sync-sales');

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'statistics' => [
                'processed' => 1,
                'books_not_found' => 0,
                'inventory_not_found' => 0
            ]
        ]);

        // Assert that a wallet transaction was created
        $this->assertEquals(1, WalletTransaction::count());
        
        // Check the transaction details
        $transaction = WalletTransaction::first();
        $this->assertEquals($user->id, $transaction->user_id);
        $this->assertEquals($book->id, $transaction->book_id);
        $this->assertEquals('sale', $transaction->type);
        // Author gets raw unit cost price * quantity: 8835.25 * 2 = 17670.50
        $this->assertEquals(17670.50, $transaction->amount);
        $this->assertEquals(2, $transaction->meta['quantity_sold']);
        $this->assertEquals(8835.25, $transaction->meta['unit_cost_price']);
        $this->assertEquals(5000, $transaction->meta['selling_price']);
    }

    /**
     * Test that sales sync skips books not found in inventory
     *
     * @return void
     */
    public function test_sales_sync_skips_books_not_found_in_inventory()
    {
        // Create a user and book
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9780241217931',
            'title' => 'Non-existent Book'
        ]);

        // Mock the RevService
        $mockRevService = Mockery::mock('App\Services\RevService');
        $this->app->instance('App\Services\RevService', $mockRevService);
        
        // Mock getSalesItems to return a sale record
        $mockRevService->shouldReceive('getSalesItems')
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'Barcode' => '9780241217931',
                            'SID' => 'SALE002',
                            'quantity_sold' => 1,
                            'SellingPrice' => 5000,
                            'total_amount' => 5000
                        ]
                    ]
                ]
            ]);
            
        // Mock getStockList to return empty inventory data (no matching products)
        $mockRevService->shouldReceive('getStockList')
            ->with([])
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'Product' => 'Different Book',
                            'UnitCostPrice' => '5000',
                            'UnitsInStock' => 3
                        ]
                    ]
                ]
            ]);

        // Call the sync sales endpoint
        $response = $this->getJson('/api/rev/sync-sales');

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'statistics' => [
                'processed' => 0,
                'inventory_not_found' => 1
            ]
        ]);

        // Assert that no wallet transaction was created
        $this->assertEquals(0, WalletTransaction::count());
    }

    /**
     * Test that sales sync skips books not found in system
     *
     * @return void
     */
    public function test_sales_sync_skips_books_not_found_in_system()
    {
        // Mock the RevService
        $mockRevService = Mockery::mock('App\Services\RevService');
        $this->app->instance('App\Services\RevService', $mockRevService);
        
        // Mock getSalesItems to return a sale record for a non-existent book
        $mockRevService->shouldReceive('getSalesItems')
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'Barcode' => '9999999999999',
                            'SID' => 'SALE003',
                            'quantity_sold' => 1,
                            'SellingPrice' => 5000,
                            'total_amount' => 5000
                        ]
                    ]
                ]
            ]);
            
        // Mock getStockList to return inventory data
        $mockRevService->shouldReceive('getStockList')
            ->with([])
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => []
                ]
            ]);

        // Call the sync sales endpoint
        $response = $this->getJson('/api/rev/sync-sales');

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'statistics' => [
                'processed' => 0,
                'books_not_found' => 1
            ]
        ]);

        // Assert that no wallet transaction was created
        $this->assertEquals(0, WalletTransaction::count());
    }

    /**
     * Clean up Mockery after each test
     */
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}