<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Services\RevService;
use Illuminate\Support\Facades\Log;

class ERPRevBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get authors who can submit books
        $authors = User::role('author')->get();
        
        if ($authors->isEmpty()) {
            $this->command->info('No authors found. Please run DemoDataSeeder first.');
            return;
        }
        
        // Initialize RevService to get categories
        $revService = new RevService();
        $categoriesResult = $revService->getItemCategories();
        
        // Get categories from ERPRev or use defaults
        $categories = [];
        if ($categoriesResult['success']) {
            $categories = $categoriesResult['categories'] ?? [];
        }
        
        // If we couldn't fetch from API, use default categories
        if (empty($categories)) {
            $categories = [
                ['id' => null, 'name' => 'Fiction'],
                ['id' => null, 'name' => 'Non-Fiction'],
                ['id' => null, 'name' => 'Mystery'],
                ['id' => null, 'name' => 'Romance'],
                ['id' => null, 'name' => 'Science Fiction'],
                ['id' => null, 'name' => 'Fantasy'],
                ['id' => null, 'name' => 'Biography'],
                ['id' => null, 'name' => 'Business'],
                ['id' => null, 'name' => 'Self-Help'],
                ['id' => null, 'name' => 'Health'],
                ['id' => null, 'name' => 'History'],
                ['id' => null, 'name' => 'Travel']
            ];
        }
        
        $this->command->info('Creating ERPRev-aligned books for ' . $authors->count() . ' authors...');
        $this->command->info('Using ' . count($categories) . ' categories from ERPRev API.');
        
        // Create books for each author
        $bookCount = 0;
        foreach ($authors as $author) {
            // Create 2-4 books per author
            $authorBookCount = rand(2, 4);
            
            for ($i = 0; $i < $authorBookCount; $i++) {
                // Select a random category
                $category = $categories[array_rand($categories)];
                $genre = is_array($category) ? $category['name'] : $category;
                
                // Generate book data that aligns with ERPRev registerProduct service
                $bookData = [
                    'user_id' => $author->id,
                    'isbn' => $this->generateISBN(),
                    'title' => $this->generateBookTitle($genre, $author->name, $i + 1),
                    'genre' => $genre,
                    'price' => $this->generatePrice(),
                    'book_type' => $this->generateBookType(),
                    'description' => $this->generateDescription($genre),
                    'status' => 'pending', // All books are pending for admin approval
                    'admin_notes' => 'Submitted for ERPRev integration review.',
                ];
                
                // Create the book
                $book = Book::create($bookData);
                $bookCount++;
                
                $this->command->info("Created book: {$book->title} (Genre: {$book->genre})");
            }
        }
        
        $this->command->info("Successfully created {$bookCount} ERPRev-aligned books!");
        $this->command->info("All books are set to 'pending' status for admin approval.");
    }
    
    /**
     * Generate a random ISBN-13
     */
    private function generateISBN(): string
    {
        // Generate a random 10-digit number
        $randomDigits = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        return '978' . $randomDigits;
    }
    
    /**
     * Generate a book title based on genre and author
     */
    private function generateBookTitle(string $genre, string $authorName, int $bookNumber): string
    {
        $adjectives = ['Amazing', 'Incredible', 'Fantastic', 'Brilliant', 'Extraordinary', 'Remarkable'];
        $nouns = ['Journey', 'Adventure', 'Story', 'Tale', 'Chronicle', 'Saga'];
        
        $adjective = $adjectives[array_rand($adjectives)];
        $noun = $nouns[array_rand($nouns)];
        
        return "{$adjective} {$genre} {$noun} #{$bookNumber}";
    }
    
    /**
     * Generate a realistic book price
     */
    private function generatePrice(): float
    {
        // Prices between $9.99 and $29.99
        return rand(9, 29) + (rand(0, 99) / 100);
    }
    
    /**
     * Generate a book type
     */
    private function generateBookType(): string
    {
        $types = ['digital', 'physical', 'both'];
        return $types[array_rand($types)];
    }
    
    /**
     * Generate a book description based on genre
     */
    private function generateDescription(string $genre): string
    {
        $descriptions = [
            'Fiction' => 'A captivating work of fiction that explores the depths of human emotion and experience.',
            'Non-Fiction' => 'An informative non-fiction work based on real events and thorough research.',
            'Mystery' => 'A thrilling mystery that will keep readers guessing until the very end.',
            'Romance' => 'A heartwarming romance story about love conquering all obstacles.',
            'Science Fiction' => 'A visionary science fiction novel set in a future world of advanced technology.',
            'Fantasy' => 'An enchanting fantasy tale filled with magic, mythical creatures, and epic adventures.',
            'Biography' => 'An inspiring biography chronicling the life and achievements of a remarkable individual.',
            'Business' => 'A practical business guide offering insights and strategies for professional success.',
            'Self-Help' => 'A transformative self-help book designed to improve your life and mindset.',
            'Health' => 'A comprehensive health guide with tips and advice for better wellness.',
            'History' => 'A fascinating historical account of significant events and figures from the past.',
            'Travel' => 'An engaging travel memoir exploring different cultures and destinations around the world.'
        ];
        
        return $descriptions[$genre] ?? "A compelling {$genre} book that offers readers an engaging and memorable experience.";
    }
}