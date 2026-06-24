<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookStatusChanged;
use Database\Seeders\RolePermissionSeeder;

class BookStatusChangeNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the roles and permissions
        $this->seed(RolePermissionSeeder::class);
    }

    /** @test */
    public function it_sends_notification_to_author_when_admin_changes_book_status()
    {
        // Prevent notifications from being sent
        Notification::fake();

        // Create an author user
        $author = User::factory()->create();
        $author->assignRole('author');

        // Create admin user
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create a book
        $book = Book::factory()->create([
            'user_id' => $author->id,
            'title' => 'Test Book',
            'status' => 'pending_review',
        ]);

        // Change book status as admin
        $response = $this->actingAs($admin)->patch(route('admin.books.review', $book), [
            'status' => 'approved_awaiting_delivery',
            'admin_notes' => 'Approved for testing purposes'
        ]);

        // Assert that notification was sent to author
        Notification::assertSentTo(
            $author,
            BookStatusChanged::class,
            function ($notification, $channels) use ($book) {
                return $notification->book->id === $book->id && 
                       $notification->newStatus === 'approved_awaiting_delivery' &&
                       $notification->oldStatus === 'pending_review';
            }
        );
    }
}