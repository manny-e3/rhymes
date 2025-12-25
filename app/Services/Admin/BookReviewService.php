<?php

namespace App\Services\Admin;

use App\Models\Book;
use App\Models\User;
use App\Notifications\BookStatusChanged;
use App\Services\RevService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class BookReviewService
{
    private $revService;

    public function __construct(RevService $revService)
    {
        $this->revService = $revService;
    }

    /**
     * Get paginated books for review with filters
     */
    public function getBooksForReview(array $filters = []): LengthAwarePaginator
    {
        Log::info('BookReviewService: getBooksForReview called', [
            'filters' => $filters,
            'timestamp' => now()->toISOString(),
        ]);
        
        $query = Book::with('user');
        
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }
        
        $result = $query->latest()->paginate(15);
        
        Log::info('BookReviewService: getBooksForReview completed', [
            'total_results' => $result->total(),
            'current_page' => $result->currentPage(),
            'per_page' => $result->perPage(),
            'timestamp' => now()->toISOString(),
        ]);
        
        return $result;
    }

    /**
     * Review a book and update its status
     */
    public function reviewBook(Book $book, array $data, User $admin): bool
    {
        Log::info('BookReviewService: reviewBook process started', [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'current_status' => $book->status,
            'data' => $data,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'timestamp' => now()->toISOString(),
        ]);
        
        try {
            $this->validateReviewData($data);
            
            $oldStatus = $book->status;
            
            // Update book status
            $updated = $book->update($data);
            
            Log::info('BookReviewService: book database update result', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'updated' => $updated,
                'old_status' => $oldStatus,
                'new_status' => $data['status'] ?? null,
                'admin_notes_provided' => $data['admin_notes'] ?? 'NOT_PROVIDED',
                'admin_notes_in_db_before_refresh' => $book->admin_notes ?? 'NULL_IN_DB',
                'admin_id' => $admin->id,
                'timestamp' => now()->toISOString(),
            ]);
            
            if ($updated) {
                // Save quantity if provided and status is stocked
                if ($data['status'] === 'stocked' && isset($data['quantity'])) {
                    $book->update(['quantity' => $data['quantity']]);
                    Log::info('BookReviewService: Quantity saved', [
                        'book_id' => $book->id,
                        'quantity' => $data['quantity'],
                        'admin_id' => $admin->id,
                    ]);
                }
                
                // If book is stocked, register it as a product in ERPREV (without quantity)
                if ($data['status'] === 'stocked') {
                    Log::info('BookReviewService: Registering book in ERPREV', [
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'book_data' => [
                            'title' => $book->title,
                            'isbn' => $book->isbn,
                            'price' => $book->price,
                            'book_type' => $book->book_type,
                            'genre' => $book->genre,
                            'description' => substr($book->description ?? '', 0, 100),
                            'author' => $book->user->name ?? 'Unknown',
                        ],
                        'admin_id' => $admin->id,
                    ]);
                    
                    // Register product without quantity
                    $result = $this->revService->registerProduct($book);
                    
                    Log::info('BookReviewService: ERPREV registration result', [
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'registration_result' => $result,
                        'admin_id' => $admin->id,
                    ]);
                    
                    if ($result['success']) {
                        // Update the book with the ERPREV product ID
                        $book->update(['rev_book_id' => $result['product_id']]);
                        Log::info('BookReviewService: Book registered in ERPREV successfully', [
                            'book_id' => $book->id,
                            'book_title' => $book->title,
                            'product_id' => $result['product_id'],
                            'admin_id' => $admin->id,
                        ]);
                    } else {
                        Log::error('BookReviewService: Failed to register book in ERPREV', [
                            'book_id' => $book->id,
                            'book_title' => $book->title,
                            'error' => $result['message'],
                            'admin_id' => $admin->id,
                        ]);
                        
                        // Add a notification to the admin about the failure
                        // This will help admins understand why the book isn't in ERPREV
                    }
                }
                
                // Handle author promotion if first book is approved for delivery and user is not already an author
                if ($data['status'] === 'approved_awaiting_delivery' && !$book->user->hasRole('author') && !$book->user->hasRole('admin')) {
                    Log::info('BookReviewService: Promoting user to author', [
                        'user_id' => $book->user->id,
                        'user_name' => $book->user->name,
                        'book_id' => $book->id, 
                        'book_title' => $book->title,
                        'admin_id' => $admin->id,
                    ]);
                    $this->promoteUserToAuthor($book->user);
                }
                
                // Refresh the book model to ensure we have the latest data
                $book->refresh();
                
                Log::info('BookReviewService: Book model after refresh', [
                    'book_id' => $book->id,
                    'admin_notes_after_refresh' => $book->admin_notes ?? 'NULL_AFTER_REFRESH',
                    'admin_id' => $admin->id,
                ]);
                
                // Send notification to the author
                Log::info('BookReviewService: Sending notification to author', [
                    'user_id' => $book->user->id,
                    'user_name' => $book->user->name,
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'old_status' => $oldStatus,
                    'new_status' => $data['status'],
                    'admin_notes' => $data['admin_notes'] ?? null,
                    'admin_notes_from_db' => $book->admin_notes ?? null,
                    'admin_id' => $admin->id,
                ]);
                $book->user->notify(new BookStatusChanged($book, $oldStatus, $data['status'], $data['admin_notes'] ?? null));
                
                Log::info('BookReviewService: reviewBook process completed successfully', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'old_status' => $oldStatus,
                    'new_status' => $data['status'],
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'timestamp' => now()->toISOString(),
                ]);
            } else {
                Log::warning('BookReviewService: Book update returned false', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'data' => $data,
                    'admin_id' => $admin->id,
                ]);
            }
            
            return $updated;
        } catch (\Exception $e) {
            Log::error('BookReviewService: reviewBook process failed with exception', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'data' => $data,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'timestamp' => now()->toISOString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Promote user to author role
     */
    private function promoteUserToAuthor(User $user): void
    {
        try {
            Log::info('BookReviewService: Starting user promotion to author', [
                'user_id' => $user->id,
                'user_name' => $user->name,
            ]);
            
            $user->assignRole('author');
            $user->update([
                'promoted_to_author_at' => now()
            ]);
            
            Log::info('BookReviewService: User promoted to author successfully', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'promoted_at' => $user->promoted_to_author_at,
            ]);
        } catch (\Exception $e) {
            Log::error('BookReviewService: Failed to promote user to author', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Validate review data
     */
    private function validateReviewData(array $data): void
    {
        Log::debug('BookReviewService: Validating review data', $data);
        
        $validStatuses = ['pending_review', 'send_review_copy', 'rejected', 'approved_awaiting_delivery', 'stocked'];
        
        if (!isset($data['status']) || !in_array($data['status'], $validStatuses)) {
            Log::error('BookReviewService: Invalid book status provided', [
                'provided_status' => $data['status'] ?? null,
                'valid_statuses' => $validStatuses,
            ]);
            throw new \InvalidArgumentException('Invalid book status');
        }
        
        Log::debug('BookReviewService: Review data validation passed');
    }

    /**
     * Get book statistics for admin dashboard
     */
    public function getBookStatistics(): array
    {
        Log::info('BookReviewService: getBookStatistics called');
        
        $statistics = [
            'total_books' => Book::count(),
            'pending_review_books' => Book::where('status', 'pending_review')->count(),
            'send_review_copy_books' => Book::where('status', 'send_review_copy')->count(),
            'rejected_books' => Book::where('status', 'rejected')->count(),
            'approved_awaiting_delivery_books' => Book::where('status', 'approved_awaiting_delivery')->count(),
            'stocked_books' => Book::where('status', 'stocked')->count(),
        ];
        
        Log::info('BookReviewService: getBookStatistics completed', $statistics);
        
        return $statistics;
    }
}