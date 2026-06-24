<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Mockery;

class ErpRevControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $adminRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
    }

    public function test_admin_can_access_products_page_and_apply_filters()
    {
        // Mock the RevService
        $mockRevService = Mockery::mock('App\Services\RevService');
        $this->app->instance('App\Services\RevService', $mockRevService);
        
        // Mock getProductsList to return product listings for multiple requests
        $mockRevService->shouldReceive('getProductsList')
            ->times(5)
            ->andReturn([
                'success' => true,
                'data' => [
                    'records' => [
                        [
                            'ID' => 'PROD001',
                            'Name' => 'Rich Dad Poor Dad',
                            'Barcode' => '1111111111111',
                            'Category' => 'Business',
                            'UnitsInStock' => 10
                        ],
                        [
                            'ID' => 'PROD002',
                            'Name' => 'Think and Grow Rich',
                            'Barcode' => '2222222222222',
                            'Category' => 'Finance',
                            'UnitsInStock' => 5
                        ],
                        [
                            'ID' => 'PROD003',
                            'Name' => 'The Alchemist',
                            'Barcode' => '3333333333333',
                            'Category' => 'Fiction',
                            'UnitsInStock' => 20
                        ]
                    ]
                ]
            ]);

        // 1. Visit products page without filters
        $response = $this->actingAs($this->admin)->get(route('admin.erprev.products'));
        $response->assertStatus(200);
        $response->assertSee('Rich Dad Poor Dad');
        $response->assertSee('Think and Grow Rich');
        $response->assertSee('The Alchemist');
        
        // 2. Filter by Product ID (id=PROD002)
        $response = $this->actingAs($this->admin)->get(route('admin.erprev.products', ['id' => 'PROD002']));
        $response->assertStatus(200);
        $response->assertSee('Think and Grow Rich');
        $response->assertDontSee('Rich Dad Poor Dad');
        $response->assertDontSee('The Alchemist');

        // 3. Filter by Name (name=Alchemist)
        $response = $this->actingAs($this->admin)->get(route('admin.erprev.products', ['name' => 'Alchemist']));
        $response->assertStatus(200);
        $response->assertSee('The Alchemist');
        $response->assertDontSee('Rich Dad Poor Dad');
        $response->assertDontSee('Think and Grow Rich');

        // 4. Filter by Barcode (barcode=1111111111111)
        $response = $this->actingAs($this->admin)->get(route('admin.erprev.products', ['barcode' => '1111111111111']));
        $response->assertStatus(200);
        $response->assertSee('Rich Dad Poor Dad');
        $response->assertDontSee('Think and Grow Rich');
        $response->assertDontSee('The Alchemist');

        // 5. Filter by ID and Name mismatch (should show no products)
        $response = $this->actingAs($this->admin)->get(route('admin.erprev.products', ['id' => 'PROD001', 'name' => 'Alchemist']));
        $response->assertStatus(200);
        $response->assertSee('No products found');
        $response->assertDontSee('Rich Dad Poor Dad');
        $response->assertDontSee('The Alchemist');
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
