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

        // Assert that wallet transactions were created (author sale + platform fee)
        $this->assertEquals(2, WalletTransaction::count());
        
        // Check the transaction details for the author sale
        $transaction = WalletTransaction::where('type', 'sale')->first();
        $this->assertEquals($user->id, $transaction->user_id);
        $this->assertEquals($book->id, $transaction->book_id);
        $this->assertEquals('sale', $transaction->type);
        // 75% of total_amount (10000) = 7500
        $this->assertEquals(7500, $transaction->amount);

        // Check the platform fee transaction
        $platformTx = WalletTransaction::where('type', 'adjustment')->first();
        $this->assertEquals('adjustment', $platformTx->type);
        // 25% of total_amount (10000) = 2500
        $this->assertEquals(2500, $platformTx->amount);

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
     * Test that SyncRevSalesJob processes sales and creates platform fee under admin
     *
     * @return void
     */
    public function test_sync_rev_sales_job_processes_sales_via_barcode_and_handles_platform_fee()
    {
        // Setup roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create admin (platform user) and author
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9780241217931',
            'title' => 'Test Job Book',
            'status' => 'stocked',
            'quantity' => 10
        ]);

        // Mock the RevService
        $mockRevService = Mockery::mock('App\Services\RevService');
        
        // Mock getSalesItems to return a sale record matching by barcode
        $mockRevService->shouldReceive('getSalesItems')
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'Barcode' => '9780241217931',
                            'SID' => 'SALE_JOB_001',
                            'quantity_sold' => 1,
                            'SellingPrice' => 10000,
                            'total_amount' => 10000
                        ]
                    ]
                ]
            ]);

        // Instantiate and handle the job
        $job = new \App\Jobs\SyncRevSalesJob(1);
        $job->handle($mockRevService);

        // Assert that transactions were created:
        // 1. Sale transaction for the author
        // 2. Platform fee transaction for the admin (positive amount)
        $this->assertEquals(2, WalletTransaction::count());

        $authorTx = WalletTransaction::where('type', 'sale')->first();
        $this->assertEquals($user->id, $authorTx->user_id);
        $this->assertEquals(7500, $authorTx->amount); // 75% of 10000
        $this->assertEquals('SALE_JOB_001', $authorTx->meta['erprev_sale_id']);

        $platformTx = WalletTransaction::where('type', 'adjustment')->first();
        $this->assertEquals($admin->id, $platformTx->user_id);
        $this->assertEquals(2500, $platformTx->amount); // 25% of 10000 (positive!)
        $this->assertEquals('SALE_JOB_001', $platformTx->meta['erprev_sale_id']);

        // Assert quantity was decremented
        $this->assertEquals(9, $book->fresh()->quantity);
    }

    /**
     * Test that SyncRevSalesJob terminates safely when pagination endRow does not advance.
     *
     * @return void
     */
    public function test_sync_rev_sales_job_terminates_on_non_advancing_pagination()
    {
        // Setup Spatie role
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '9780241217931',
            'title' => 'Test Job Pagination',
            'status' => 'stocked',
            'quantity' => 10
        ]);

        $mockRevService = Mockery::mock('App\Services\RevService');
        
        // Mock getSalesItems to return the same page with startRow = 5001 and count = 1
        // It will be called exactly twice:
        // 1st call: returns page 1 (1 record, endRow = 5000, TotalRecords = 5500)
        // 2nd call: returns page 2 (1 record, endRow = 5000, TotalRecords = 5500)
        // 3rd call: does not happen because startRow does not advance (stays 5001)
        $mockRevService->shouldReceive('getSalesItems')
            ->twice()
            ->andReturn(
                [
                    'success' => true,
                    'data' => [
                        'records' => [
                            [
                                'Barcode' => '9780241217931',
                                'SID' => 'SALE_PAG_1',
                                'quantity_sold' => 1,
                                'SellingPrice' => 1000,
                                'total_amount' => 1000
                            ]
                        ],
                        'pagenation' => [
                            'TotalRecords' => 5500,
                            'endRow' => 5000
                        ]
                    ]
                ],
                [
                    'success' => true,
                    'data' => [
                        'records' => [
                            [
                                'Barcode' => '9780241217931',
                                'SID' => 'SALE_PAG_2',
                                'quantity_sold' => 1,
                                'SellingPrice' => 1000,
                                'total_amount' => 1000
                            ]
                        ],
                        'pagenation' => [
                            'TotalRecords' => 5500,
                            'endRow' => 5000
                        ]
                    ]
                ]
            );

        $job = new \App\Jobs\SyncRevSalesJob(1);
        $job->handle($mockRevService);

        // Assert that 4 transactions were created (2 sales + 2 platform fees)
        $this->assertEquals(4, WalletTransaction::count());
        // Assert quantity was decremented by 2
        $this->assertEquals(8, $book->fresh()->quantity);
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