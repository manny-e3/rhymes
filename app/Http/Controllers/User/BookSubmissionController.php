<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BookService;
use App\Services\RevService;
use App\Models\Book;
use App\Models\User;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Log;

class BookSubmissionController extends Controller
{
    private BookService $bookService;
    private RevService $revService;

    public function __construct(
        BookService $bookService,
        RevService $revService
    ) {
        $this->bookService = $bookService;
        $this->revService = $revService;
        $this->middleware(['auth']);
    }

    /**
     * Show the form for creating a new book submission.
     */
    public function create()
    {
        // Fetch categories from ERPREV API
        $categoriesResult = $this->revService->getItemCategories();
        $categories = [];
        
        if ($categoriesResult['success']) {
            // Use the processed categories with both name and ID
            $categories = $categoriesResult['categories'] ?? [];
        }
        
        // If we couldn't fetch from API, use default categories
        if (empty($categories)) {
            $defaultCategories = [
                'Fiction', 'Non-Fiction', 'Mystery', 'Romance', 'Science Fiction',
                'Fantasy', 'Biography', 'Business', 'Self-Help', 'Health',
                'History', 'Travel'
            ];
            
            // Format default categories to match the new structure
            foreach ($defaultCategories as $category) {
                $categories[] = [
                    'id' => null,
                    'name' => $category
                ];
            }
        }
        
        return view('user.books.create', compact('categories'));
    }

    /**
     * Store a newly created book submission in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate(
            $this->bookService->validateBookData($request->all())
        );

        // Create book and notify admins (notification is now handled in the service)
        $book = $this->bookService->createBook($user, $validated);
        
        // Notify the user who submitted the book
        try {
            $user->notify(new BookSubmitted($book));
            Log::info('Book submission confirmation sent to author', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'book_id' => $book->id,
                'book_title' => $book->title
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send book submission confirmation to author', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'book_id' => $book->id,
                'book_title' => $book->title,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Book submitted successfully for review! You will be notified once your book is approved and you become an author.');
    }
}