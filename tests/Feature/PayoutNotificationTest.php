<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Payout;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PayoutRequested;
use App\Notifications\PayoutStatusChanged;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Log;

class PayoutNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the roles and permissions
        $this->seed(RolePermissionSeeder::class);
    }

    /** @test */
    public function it_sends_notification_to_admins_when_author_requests_payout()
    {
        // Prevent notifications from being sent
        Notification::fake();

        // Create an author user
        $author = User::factory()->create();
        $author->assignRole('author');

        // Create admin users
        $admin1 = User::factory()->create();
        $admin1->assignRole('admin');

        $admin2 = User::factory()->create();
        $admin2->assignRole('admin');

        // Create a regular user (not admin)
        $regularUser = User::factory()->create();
        $regularUser->assignRole('user');

        // Payout data
        $payoutData = [
            'amount_requested' => 50000,
            'status' => 'pending',
        ];

        // Act as the author and request a payout
        $response = $this->actingAs($author)->post(route('author.payouts.store'), $payoutData);

        // Assert the response
        $response->assertStatus(302); // Redirect back

        // Get the created payout
        $payout = Payout::first();
        
        // Assert that a payout was created
        $this->assertNotNull($payout);
        $this->assertEquals($author->id, $payout->user_id);
        $this->assertEquals(50000, $payout->amount_requested);
        $this->assertEquals('pending', $payout->status);

        // Assert that notifications were sent to admins
        Notification::assertSentTo(
            $admin1,
            PayoutRequested::class,
            function ($notification, $channels) use ($payout) {
                return $notification->payout->id === $payout->id;
            }
        );

        Notification::assertSentTo(
            $admin2,
            PayoutRequested::class,
            function ($notification, $channels) use ($payout) {
                return $notification->payout->id === $payout->id;
            }
        );

        // Assert that regular user didn't receive notification
        Notification::assertNotSentTo(
            $regularUser,
            PayoutRequested::class
        );
    }

    /** @test */
    public function it_sends_notification_to_author_when_admin_approves_payout()
    {
        // Prevent notifications from being sent
        Notification::fake();

        // Create an author user
        $author = User::factory()->create();
        $author->assignRole('author');

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create a payout
        $payout = Payout::factory()->create([
            'user_id' => $author->id,
            'amount_requested' => 50000,
            'status' => 'pending',
        ]);

        // Approve the payout as admin
        $response = $this->actingAs($admin)->patch(route('admin.payouts.approve', $payout), [
            'admin_notes' => 'Approved for testing purposes'
        ]);

        // Assert the response
        $response->assertStatus(302); // Redirect back

        // Assert that notification was sent to author
        Notification::assertSentTo(
            $author,
            PayoutStatusChanged::class,
            function ($notification, $channels) use ($payout) {
                return $notification->payout->id === $payout->id && 
                       $notification->newStatus === 'approved';
            }
        );
    }

    /** @test */
    public function it_sends_notification_to_author_when_admin_denies_payout()
    {
        // Prevent notifications from being sent
        Notification::fake();

        // Create an author user
        $author = User::factory()->create();
        $author->assignRole('author');

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create a payout
        $payout = Payout::factory()->create([
            'user_id' => $author->id,
            'amount_requested' => 50000,
            'status' => 'pending',
        ]);

        // Deny the payout as admin
        $response = $this->actingAs($admin)->post(route('admin.payouts.deny', $payout), [
            'admin_notes' => 'Denied for testing purposes'
        ]);

        // Assert the response
        $response->assertStatus(302); // Redirect back

        // Assert that notification was sent to author
        Notification::assertSentTo(
            $author,
            PayoutStatusChanged::class,
            function ($notification, $channels) use ($payout) {
                return $notification->payout->id === $payout->id && 
                       $notification->newStatus === 'denied';
            }
        );
    }
}