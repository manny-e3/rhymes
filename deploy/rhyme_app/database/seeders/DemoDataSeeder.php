<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\WalletTransaction;
use App\Models\Payout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate([
            'email' => 'admin@rhymes.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        // Ensure admin has the admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Create author user
        $author = User::firstOrCreate([
            'email' => 'author@rhymes.com',
        ], [
            'name' => 'John Author',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+1234567890',
            'payment_details' => [
                'bank_name' => 'Demo Bank',
                'account_number' => '1234567890'
            ],
            'promoted_to_author_at' => now(),
        ]);
        
        // Ensure author has the author role
        if (!$author->hasRole('author')) {
            $author->assignRole('author');
        }

        // Create additional authors
        $authors = [];
        $authors[] = $author; // Add the first author
        
        $author2 = User::firstOrCreate([
            'email' => 'sarah@rhymes.com',
        ], [
            'name' => 'Sarah Writer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+1987654321',
            'payment_details' => [
                'bank_name' => 'Demo Bank',
                'account_number' => '9876543210'
            ],
            'promoted_to_author_at' => now(),
        ]);
        $author2->assignRole('author');
        $authors[] = $author2;

        $author3 = User::firstOrCreate([
            'email' => 'michael@rhymes.com',
        ], [
            'name' => 'Michael Pen',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone' => '+1555666777',
            'payment_details' => [
                'bank_name' => 'Demo Bank',
                'account_number' => '5556667770'
            ],
            'promoted_to_author_at' => now(),
        ]);
        $author3->assignRole('author');
        $authors[] = $author3;

        // Create more authors using factory
        $additionalAuthors = User::factory()->count(7)->create()->each(function ($user) {
            $user->assignRole('author');
            $user->update([
                'payment_details' => [
                    'bank_name' => 'Demo Bank',
                    'account_number' => Str::random(10)
                ],
                'promoted_to_author_at' => now(),
            ]);
        });
        
        foreach ($additionalAuthors as $addAuthor) {
            $authors[] = $addAuthor;
        }

        // Create regular users
        $users = [];
        
        $user = User::firstOrCreate([
            'email' => 'user@rhymes.com',
        ], [
            'name' => 'Regular User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
        $users[] = $user;

        // Create additional regular users
        $user2 = User::firstOrCreate([
            'email' => 'jane@rhymes.com',
        ], [
            'name' => 'Jane Reader',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user');
        $users[] = $user2;

        $user3 = User::firstOrCreate([
            'email' => 'bob@rhymes.com',
        ], [
            'name' => 'Bob Customer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user3->assignRole('user');
        $users[] = $user3;

        // Create more users using factory
        $additionalUsers = User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });
        
        foreach ($additionalUsers as $addUser) {
            $users[] = $addUser;
        }

        // Create sample books for authors using ERPRev categories
        $books = [];
        
        // Initialize RevService to get categories
        $revService = new \App\Services\RevService();
        $categoriesResult = $revService->getItemCategories();
        
        // Get categories from ERPRev or use defaults
        $genres = [];
        if ($categoriesResult['success']) {
            $categories = $categoriesResult['categories'] ?? [];
            foreach ($categories as $category) {
                if (is_array($category)) {
                    $genres[] = $category['name'];
                } else {
                    $genres[] = $category;
                }
            }
        }
        
        // If we couldn't fetch from API, use default categories
        if (empty($genres)) {
            $genres = ['Fiction', 'Business', 'Mystery', 'Science Fiction', 'Romance', 'Cooking', 'Biography', 'History', 'Fantasy', 'Thriller'];
        }
        
        $statuses = ['pending', 'accepted', 'stocked'];
        $types = ['digital', 'physical', 'both'];
        
        // Create books for each author
        foreach ($authors as $index => $auth) {
            // Create 3-5 books per author
            $bookCount = rand(3, 5);
            
            for ($i = 0; $i < $bookCount; $i++) {
                $genre = $genres[array_rand($genres)];
                $type = $types[array_rand($types)];
                $status = $statuses[array_rand($statuses)];
                
                $book = Book::firstOrCreate([
                    'isbn' => '978' . str_pad(($index * 1000 + $i + 1), 10, '0', STR_PAD_LEFT),
                ], [
                    'user_id' => $auth->id,
                    'title' => $genre . ' Book #' . ($i + 1) . ' by ' . $auth->name,
                    'genre' => $genre,
                    'price' => rand(9, 29) + (rand(0, 99) / 100),
                    'book_type' => $type,
                    'description' => 'This is a sample ' . $genre . ' book created for demonstration purposes. It showcases the kind of content you might find in a typical ' . strtolower($genre) . ' publication.',
                    'status' => $status,
                    'admin_notes' => $status == 'pending' ? 'Under review.' : ($status == 'accepted' ? 'Approved for stocking.' : 'Available for sale.'),
                    'rev_book_id' => 'REV-' . str_pad(($index * 100 + $i + 1), 4, '0', STR_PAD_LEFT),
                ]);
                
                $books[] = $book;
            }
        }

        // Create wallet transactions (sales) for books
        foreach ($books as $book) {
            // Skip pending books
            if ($book->status === 'pending') {
                continue;
            }
            
            // Create 2-8 sales per book
            $salesCount = rand(2, 8);
            
            for ($i = 0; $i < $salesCount; $i++) {
                $buyer = $users[array_rand($users)];
                
                WalletTransaction::create([
                    'user_id' => $book->user_id,
                    'book_id' => $book->id,
                    'type' => 'sale',
                    'amount' => $book->price * (rand(80, 120) / 100), // Slight variation in price
                    'meta' => [
                        'sale_date' => now()->subDays(rand(1, 30)),
                        'customer_location' => fake()->city() . ', ' . fake()->stateAbbr(),
                        'sale_type' => $book->book_type == 'both' ? (rand(0, 1) ? 'physical' : 'digital') : $book->book_type
                    ],
                ]);
            }
        }

        // Create some payouts for authors with significant earnings
        foreach ($authors as $author) {
            $balance = $author->getWalletBalance();
            
            // Only create payouts for authors with balance > ₦20
            if ($balance > 20) {
                // Create 1-2 payouts per eligible author
                $payoutCount = rand(1, 2);
                
                for ($i = 0; $i < $payoutCount; $i++) {
                    $amount = min($balance * (rand(30, 80) / 100), $balance); // Request 30-80% of balance
                    
                    Payout::create([
                        'user_id' => $author->id,
                        'amount_requested' => $amount,
                        'status' => $i == 0 ? 'completed' : 'pending', // First payout completed, others pending
                        'admin_notes' => $i == 0 ? 'Processed successfully.' : 'Awaiting approval.',
                        'processed_at' => $i == 0 ? now()->subDays(rand(1, 10)) : null,
                    ]);
                    
                    // If completed, create a corresponding wallet transaction
                    if ($i == 0) {
                        WalletTransaction::create([
                            'user_id' => $author->id,
                            'type' => 'payout',
                            'amount' => -$amount, // Negative amount for payouts
                            'meta' => [
                                'payout_id' => Payout::latest()->first()->id,
                                'processed_date' => now()->subDays(rand(1, 10))
                            ],
                        ]);
                    }
                }
            }
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('Admin: admin@rhymes.com / password');
        $this->command->info('Authors:');
        $this->command->info('  - author@rhymes.com / password');
        $this->command->info('  - sarah@rhymes.com / password');
        $this->command->info('  - michael@rhymes.com / password');
        $this->command->info('Users:');
        $this->command->info('  - user@rhymes.com / password');
        $this->command->info('  - jane@rhymes.com / password');
        $this->command->info('  - bob@rhymes.com / password');
        $this->command->info('');
        $this->command->info('Additionally created:');
        $this->command->info('  - 7 more authors');
        $this->command->info('  - 10 more users');
        $this->command->info('  - Multiple books per author');
        $this->command->info('  - Sales transactions');
        $this->command->info('  - Payout records');
    }
}