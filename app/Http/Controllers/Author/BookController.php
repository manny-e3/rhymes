<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BookService;
use App\Services\RevService;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    private BookService $bookService;
    private RevService $revService;

    public function __construct(
        BookService $bookService,
        RevService $revService
    ) {
        $this->bookService = $bookService;
        $this->revService = $revService;
        $this->middleware(['auth', 'role:author|admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
         $books = $this->bookService->getUserBooks($user);
       
        
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
        
        return view('author.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
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
        
        return view('author.books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        try {
            $validated = $request->validate(
                $this->bookService->validateBookData($request->all())
            );

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('books'), $filename);
                $validated['image'] = 'books/' . $filename;
            }

            $this->bookService->createBook($user, $validated);

            return redirect()->route('author.books.index')
                ->with('success', 'Book submitted successfully for review!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }
            
            $errorMessage = implode('\n', $errorMessages);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', $errorMessage ?: 'There were validation errors in your submission. Please check the form and try again.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Book creation error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your book. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $this->authorize('view', $book);
        return view('author.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $this->authorize('update', $book);
        
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
        
        return view('author.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);
        
        try {
            $validated = $request->validate(
                $this->bookService->validateBookData($request->all(), $book)
            );

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($book->image) {
                    $oldPath = public_path($book->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $file = $request->file('image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('books'), $filename);
                $validated['image'] = 'books/' . $filename;
            }

            $this->bookService->updateBook($book, $validated);

            return redirect()->route('author.books.index')
                ->with('success', 'Book updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }
            
            $errorMessage = implode('\n', $errorMessages);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', $errorMessage ?: 'There were validation errors in your submission. Please check the form and try again.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Book update error: ' . $e->getMessage(), [
                'book_id' => $book->id,
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating your book. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        
        try {
            // Check if book has any transactions before deleting
            if ($book->walletTransactions()->count() > 0) {
                return redirect()->route('author.books.index')
                    ->with('error', 'Cannot delete book with existing transactions.');
            }
            
            $this->bookService->deleteBook($book);

            return redirect()->route('author.books.index')
                ->with('success', 'Book deleted successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Book deletion error: ' . $e->getMessage(), [
                'book_id' => $book->id,
                'exception' => $e
            ]);
            
            return redirect()->route('author.books.index')
                ->with('error', 'An error occurred while deleting your book. Please try again.');
        }
    }

    /**
     * Restore a soft deleted book
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        try {
            $book = $this->bookService->getBookByIdWithTrashed($id);
            
            if (!$book) {
                return redirect()->route('author.books.index')
                    ->with('error', 'Book not found.');
            }
            
            $this->authorize('delete', $book);
            
            if ($book->trashed()) {
                $this->bookService->restoreBook($book);
                return redirect()->route('author.books.index')
                    ->with('success', 'Book restored successfully!');
            }
            
            return redirect()->route('author.books.index')
                ->with('error', 'Book is not deleted.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Book restoration error: ' . $e->getMessage(), [
                'book_id' => $id,
                'exception' => $e
            ]);
            
            return redirect()->route('author.books.index')
                ->with('error', 'An error occurred while restoring your book. Please try again.');
        }
    }
    
    /**
     * Request to retrieve a book
     */
    public function requestRetrieval(Request $request, Book $book)
    {
        $this->authorize('recall', $book); // Keep using 'recall' policy for now or rename later
        
        try {
            $validated = $request->validate([
                'retrieval_reason' => 'nullable|string|max:1000',
                'retrieval_location' => 'required|string|max:255',
                'retrieval_quantity' => 'required|integer|min:1',
            ]);
            
            // Update the book with retrieval request information and change status to retrieval_requested
            $book->update([
                'retrieval_requested' => true,
                'retrieval_reason' => $validated['retrieval_reason'] ?? null,
                'retrieval_location' => $validated['retrieval_location'],
                'retrieval_quantity' => $validated['retrieval_quantity'],
                'retrieval_requested_at' => now(),
                'status' => 'retrieval_requested',
            ]);
            
            // Notify admins about the retrieval request
            $this->bookService->notifyAdminsAboutBookRetrieval($book, $validated['retrieval_reason'] ?? null);
            
            return response()->json([
                'success' => true,
                'message' => 'Retrieval request sent successfully. Admins have been notified.',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Book retrieval request error: ' . $e->getMessage(), [
                'book_id' => $book->id,
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the retrieval request. Please try again.',
            ], 500);
        }
    }
}
