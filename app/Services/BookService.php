<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;
use App\Notifications\BookSubmitted;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BookService
{
    /**
     * Get paginated books for user (excluding soft deleted)
     */
    public function getUserBooks(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return Book::with(['walletTransactions'])
            ->where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create a new book and notify admins
     */
    public function createBook(User $user, array $data): Book
    {
        $data['user_id'] = $user->id;
        $data['status'] = 'pending_review'; 

        $book = Book::create($data);
        
        // Notify admins about the new book submission
        $this->notifyAdminsAboutNewBook($book, $user);
        
        return $book;
    }

    /**
     * Notify all admins about a new book submission
     */
    private function notifyAdminsAboutNewBook(Book $book, User $author): void
    {
        try {
            // Get all admins
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();
            
            Log::info('Notifying admins about new book submission', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'author_id' => $author->id,
                'author_name' => $author->name,
                'admin_count' => $admins->count()
            ]);
            
            // Load the user relationship for the notification
            $book->load('user');
            
            // Notify each admin
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new BookSubmitted($book));
                    Log::info('Book submission notification sent to admin', [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'admin_email' => $admin->email,
                        'book_id' => $book->id,
                        'book_title' => $book->title
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send book submission notification to admin', [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'admin_email' => $admin->email,
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about new book submission', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'author_id' => $author->id,
                'author_name' => $author->name,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update a book
     */
    public function updateBook(Book $book, array $data): bool
    {
        // If the book is currently stocked and an author is updating it, 
        // notify admin but keep the status as 'stocked'
        if ($book->status === 'stocked') {
            // Store original data for comparison
            $originalData = [
                'title' => $book->title,
                'isbn' => $book->isbn,
                'genre' => $book->genre,
                'price' => $book->price,
                'book_type' => $book->book_type,
                'description' => $book->description,
            ];
            
            $data['original_data'] = $originalData;
            
            // Notify admins about the edit
            $this->notifyAdminsAboutBookEdit($book);
        }
        
        return $book->update($data);
    }
    
    /**
     * Notify all admins about a book edit that requires approval
     */
    private function notifyAdminsAboutBookEdit(Book $book): void
    {
        try {
            // Get all admins
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();
            
            
            // Notify each admin
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new \App\Notifications\BookEditedForApproval($book));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send book edit notification to admin', [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'admin_email' => $admin->email,
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify admins about book edit', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'author_id' => $book->user_id,
                'author_name' => $book->user->name ?? 'Unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a book (soft delete)
     */
    public function deleteBook(Book $book): bool
    {
        return $book->delete();
    }

    /**
     * Restore a soft deleted book
     */
    public function restoreBook(Book $book): bool
    {
        return $book->restore();
    }

    /**
     * Permanently delete a book
     */
    public function forceDeleteBook(Book $book): bool
    {
        return $book->forceDelete();
    }

    /**
     * Get book by ID (excluding soft deleted)
     */
    public function getBookById(int $id): ?Book
    {
        return Book::find($id);
    }

    /**
     * Get book by ID including soft deleted
     */
    public function getBookByIdWithTrashed(int $id): ?Book
    {
        return Book::withTrashed()->find($id);
    }

    /**
     * Get all books including soft deleted
     */
    public function getAllBooksWithTrashed(): Collection
    {
        return Book::withTrashed()->get();
    }

    /**
     * Get only soft deleted books
     */
    public function getOnlyTrashedBooks(): Collection
    {
        return Book::onlyTrashed()->get();
    }

    /**
     * Get books by status
     */
    public function getBooksByStatus(string $status): Collection
    {
        return Book::where('status', $status)->get();
    }

    /**
     * Get user books by status
     */
    public function getUserBooksByStatus(User $user, string $status): Collection
    {
        return Book::where('user_id', $user->id)
            ->where('status', $status)
            ->get();
    }

    /**
     * Get book sales analytics
     */
    public function getBookSalesAnalytics(int $bookId): array
    {
        $book = $this->getBookById($bookId);
        
        if (!$book) {
            return [
                'total_sales' => 0,
                'sales_count' => 0,
            ];
        }

        return [
            'total_sales' => $book->getTotalSales(),
            'sales_count' => $book->getSalesCount(),
        ];
    }

    /**
     * Get books with sales data for user
     */
    public function getBooksWithSalesForUser(User $user): Collection
    {
        return Book::where('user_id', $user->id)
            ->with(['walletTransactions' => function ($query) {
                $query->where('type', 'sale');
            }])
            ->get();
    }

    /**
     * Validate book data
     */
    public function validateBookData(array $data, ?Book $book = null): array
    {
        $rules = [
            'isbn' => 'required|string|unique:books',
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'book_type' => 'required|string', // Options: paperback, hardback, both
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        // If updating, exclude current book from ISBN uniqueness check and make image optional
        if ($book) {
            $rules['isbn'] = 'required|string|unique:books,isbn,' . $book->id;
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';
        }

        return $rules;
    }

    /**
     * Check if user can perform action on book
     */
    public function canUserAccessBook(User $user, Book $book): bool
    {
        return $book->user_id === $user->id || $user->hasRole('admin');
    }

    /**
     * Update book status (admin function)
     */
    public function updateBookStatus(Book $book, string $status, ?string $adminNotes = null): bool
    {
        $validStatuses = ['pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked', 'retrieval_requested', 'retrieved'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Invalid book status');
        }

        $data = ['status' => $status];
        
        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }

        return $this->updateBook($book, $data);
    }
    
    /**
     * Notify admins about book retrieval request
     */
    public function notifyAdminsAboutBookRetrieval(Book $book, ?string $reason = null): void
    {
        try {
            // Get all admins
            $admins = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();
            
            // Notify each admin
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new \App\Notifications\BookRetrievalNotification($book, $reason));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send book recall notification to admin', [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'admin_email' => $admin->email,
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify admins about book retrieval', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'author_id' => $book->user_id,
                'author_name' => $book->user->name ?? 'Unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get author books count
     */
    public function getAuthorBooksCount(int $userId): int
    {
        return Book::where('user_id', $userId)->count();
    }

    /**
     * Get author books by status
     */
    public function getAuthorBooksByStatus(int $userId, string $status): Collection
    {
        return Book::where('user_id', $userId)
            ->where('status', $status)
            ->get();
    }

    /**
     * Get recent books for author
     */
    public function getAuthorRecentBooks(int $userId, int $limit = 5): Collection
    {
        return Book::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }
}