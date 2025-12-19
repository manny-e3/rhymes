<?php
require_once 'vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap Laravel's database component
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Book;

echo "Checking books in database...\n";
$totalBooks = Book::count();
echo "Total books: " . $totalBooks . "\n";

$booksWithIsbn = Book::whereNotNull('isbn')->count();
echo "Books with ISBN: " . $booksWithIsbn . "\n";

// Show a few sample books with user_id
echo "\nSample books with user_id:\n";
$sampleBooks = Book::whereNotNull('isbn')->with('user')->limit(3)->get(['id', 'isbn', 'title', 'status', 'user_id']);
foreach ($sampleBooks as $book) {
    echo "- ID: {$book->id}, ISBN: {$book->isbn}, Title: {$book->title}, Status: {$book->status}, User ID: {$book->user_id}\n";
    if ($book->user) {
        echo "  User: {$book->user->name} (ID: {$book->user->id})\n";
    } else {
        echo "  User: None\n";
    }
}

// Check if any books have the specific ISBNs from ERPREV
$testIsbn = '9780241217931';
$foundBook = Book::where('isbn', $testIsbn)->with('user')->first();
if ($foundBook) {
    echo "\nFound book with ISBN {$testIsbn}:\n";
    echo "- ID: {$foundBook->id}, Title: {$foundBook->title}, Status: {$foundBook->status}, User ID: {$foundBook->user_id}\n";
    if ($foundBook->user) {
        echo "  User: {$foundBook->user->name} (ID: {$foundBook->user->id})\n";
    } else {
        echo "  User: None\n";
    }
} else {
    echo "\nNo book found with ISBN {$testIsbn}\n";
}