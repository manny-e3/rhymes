<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $adminRole;
    protected $authorRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
        $this->authorRole = Role::firstOrCreate(['name' => 'author']);
        $this->userRole = Role::firstOrCreate(['name' => 'user']);

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole($this->adminRole);
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole($this->userRole);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    public function test_admin_can_create_user()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role' => 'user',
            'email_verified' => true // Mimicking the form input usually sent
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $userData);
        
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_admin_can_update_user()
    {
        $user = User::factory()->create();
        // Assuming the controller expects 'roles' as an array of IDs or names
        // Let's check the update validation layout in a real scenario, but safely:
        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'roles' => [$this->userRole->name], // Passing role name assuming spatie/laravel-permission syncRoles logic in controller
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), $updateData);
        
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_view_payouts()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.payouts.index'));
        $response->assertStatus(200);
    }
}
