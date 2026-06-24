<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $author;
    protected $authorRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist
        $this->authorRole = Role::firstOrCreate(['name' => 'author']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // Create author user
        $this->author = User::factory()->create();
        $this->author->assignRole($this->authorRole);
    }

    public function test_author_can_access_dashboard()
    {
        $response = $this->actingAs($this->author)->get(route('author.dashboard'));
        $response->assertStatus(200);
    }

    public function test_author_can_list_books()
    {
        Book::factory()->count(3)->create(['user_id' => $this->author->id]);
        
        $response = $this->actingAs($this->author)->get(route('author.books.index'));
        $response->assertStatus(200);
        $response->assertViewHas('books');
    }

    public function test_author_can_create_book()
    {
        $bookData = Book::factory()->make()->toArray();
        // Remove user_id as it should be assigned by the controller
        unset($bookData['user_id']);
        
        $response = $this->actingAs($this->author)->post(route('author.books.store'), $bookData);
        
        $response->assertRedirect(route('author.books.index'));
        $this->assertDatabaseHas('books', ['title' => $bookData['title'], 'user_id' => $this->author->id]);
    }

    public function test_author_can_update_own_book()
    {
        $book = Book::factory()->create(['user_id' => $this->author->id]);
        
        $updateData = [
            'title' => 'Updated Title',
            'isbn' => $book->isbn,
            'genre' => $book->genre,
            'price' => $book->price,
            'book_type' => $book->book_type,
            'description' => $book->description,
        ];
        
        $response = $this->actingAs($this->author)->put(route('author.books.update', $book), $updateData);
        
        $response->assertRedirect(route('author.books.index'));
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Updated Title']);
    }

    public function test_author_cannot_update_others_book()
    {
        $otherAuthor = User::factory()->create();
        $otherAuthor->assignRole($this->authorRole);
        $book = Book::factory()->create(['user_id' => $otherAuthor->id]);
        
        $updateData = ['title' => 'Hacked Title'];
        
        $response = $this->actingAs($this->author)->put(route('author.books.update', $book), $updateData);
        
        $response->assertStatus(403);
    }

    public function test_author_can_view_wallet()
    {
        $response = $this->actingAs($this->author)->get(route('author.wallet.index'));
        $response->assertStatus(200);
    }
    
    public function test_author_can_view_profile()
    {
        $response = $this->actingAs($this->author)->get(route('author.profile.edit'));
        $response->assertStatus(200);
    }
}
