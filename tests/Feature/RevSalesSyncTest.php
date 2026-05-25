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
     * Test that sales sync processes books and deducts book quantity
     *
     * @return void
     */
    public function test_sales_sync_processes_sales_and_deducts_book_quantity()
    {
        // Create a user and book with ISBN
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9780241217931',
            'title' => '#Girlboss PB',
            'status' => 'stocked',
            'quantity' => 20
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

        // Call the sync sales endpoint
        $response = $this->getJson(route('api.erprev.sync-sales'));

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'statistics' => [
                'processed' => 1,
                'books_not_found' => 0,
                'duplicates' => 0
            ]
        ]);

        // Assert that a wallet transaction was created
        $this->assertEquals(1, WalletTransaction::count());
        
        // Check the transaction details
        $transaction = WalletTransaction::first();
        $this->assertEquals($user->id, $transaction->user_id);
        $this->assertEquals($book->id, $transaction->book_id);
        $this->assertEquals('sale', $transaction->type);
        // SellingPrice is used directly as wallet amount
        $this->assertEquals(5000, $transaction->amount);
        $this->assertEquals(2, $transaction->meta['quantity_sold']);
        $this->assertEquals(5000, $transaction->meta['selling_price']);

        // Assert book quantity was decremented
        $this->assertEquals(18, $book->fresh()->quantity);
    }

    /**
     * Test that sales sync skips duplicate sales
     *
     * @return void
     */
    public function test_sales_sync_skips_duplicate_sales()
    {
        // Create a user and book
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9780241217931',
            'title' => 'Test Book',
            'status' => 'stocked',
            'quantity' => 20
        ]);

        // Create a pre-existing transaction with the unique ID
        $uniqueId = md5('9780241217931' . '' . 'SALE002' . '');
        WalletTransaction::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'type' => 'sale',
            'amount' => 5000,
            'meta' => [
                'erprev_unique_id' => $uniqueId,
                'erprev_sid' => 'SALE002',
                'quantity_sold' => 1,
                'selling_price' => 5000
            ]
        ]);

        // Mock the RevService
        $mockRevService = Mockery::mock('App\Services\RevService');
        $this->app->instance('App\Services\RevService', $mockRevService);
        
        // Mock getSalesItems to return the same sale record
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

        // Call the sync sales endpoint
        $response = $this->getJson(route('api.erprev.sync-sales'));

        // Assert the response
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'statistics' => [
                'processed' => 0,
                'duplicates' => 1
            ]
        ]);

        // Assert that no new wallet transaction was created (still only 1)
        $this->assertEquals(1, WalletTransaction::count());

        // Assert book quantity was not decremented
        $this->assertEquals(20, $book->fresh()->quantity);
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

        // Call the sync sales endpoint
        $response = $this->getJson(route('api.erprev.sync-sales'));

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