<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;

echo "=== ERPREV Registration Verification ===\n\n";

$totalAccepted = Book::where('status', 'accepted')->count();
$registered = Book::where('status', 'accepted')->whereNotNull('rev_book_id')->count();
$unregistered = Book::where('status', 'accepted')->whereNull('rev_book_id')->count();

echo "📊 Summary:\n";
echo "   Total Accepted Books: {$totalAccepted}\n";
echo "   ✅ Registered in ERPREV: {$registered}\n";
echo "   ❌ Not Registered: {$unregistered}\n\n";

if ($unregistered === 0) {
    echo "🎉 SUCCESS! All accepted books are now registered in ERPREV!\n\n";
} else {
    echo "⚠️  WARNING: {$unregistered} book(s) still not registered.\n\n";
}

// Show details of all accepted books
echo "📚 Accepted Books Details:\n\n";
$books = Book::where('status', 'accepted')
    ->with('user')
    ->get(['id', 'title', 'isbn', 'rev_book_id', 'user_id']);

foreach ($books as $book) {
    $status = $book->rev_book_id ? '✅' : '❌';
    echo "   {$status} ID: {$book->id} | {$book->title}\n";
    echo "      ISBN: {$book->isbn}\n";
    echo "      REV Product ID: " . ($book->rev_book_id ?? 'NOT SET') . "\n";
    echo "      Author: {$book->user->name}\n\n";
}

echo "=== Verification Complete ===\n";
