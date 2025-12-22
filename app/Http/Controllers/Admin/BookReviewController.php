<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Services\Admin\BookReviewService;
use App\Services\RevService;
use Illuminate\Support\Facades\Response;

class BookReviewController extends Controller
{
    private BookReviewService $bookReviewService;
    private RevService $revService;

    public function __construct(
        BookReviewService $bookReviewService,
        RevService $revService
    ) {
        $this->bookReviewService = $bookReviewService;
        $this->revService = $revService;
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Book::with(['user', 'walletTransactions'])->whereNull('deleted_at');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        
        $books = $query->latest()->paginate(15);
        
        // Fetch categories from ERPREV API for genre filter
        $categoriesResult = $this->revService->getItemCategories();
        $genres = collect();
        
        if ($categoriesResult['success']) {
            // Use the processed categories with both name and ID
            $categories = $categoriesResult['categories'] ?? [];
            foreach ($categories as $category) {
                if (is_array($category) && isset($category['name'])) {
                    $genres->push($category['name']);
                } elseif (is_string($category)) {
                    $genres->push($category);
                }
            }
        }
        
        // If we couldn't fetch from API, use existing genres from database
        if ($genres->isEmpty()) {
            $genres = Book::distinct()->pluck('genre')->filter()->sort();
        }
        
        return view('admin.books.index', compact('books', 'genres'));
    }

    public function show(Book $book)
    {
        $book->load(['user', 'walletTransactions']);
        
        // Calculate book statistics
        $stats = [
            'total_sales' => $book->walletTransactions()->where('type', 'sale')->count(),
            'total_revenue' => $book->walletTransactions()->where('type', 'sale')->sum('amount'),
            'average_sale_price' => $book->walletTransactions()->where('type', 'sale')->avg('amount') ?? 0,
        ];
        
        return view('admin.books.show', compact('book', 'stats'));
    }

    public function review(Request $request, Book $book)
    {
        $admin = Auth::user();
        
        // Log the incoming request data for debugging
        Log::info('Book review process started', [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'current_status' => $book->status,
            'request_data' => $request->all(),
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'timestamp' => now()->toISOString(),
        ]);
        
        // Log raw input
        $rawInput = file_get_contents('php://input');
        Log::debug('Book review raw input data', [
            'book_id' => $book->id,
            'raw_input' => $rawInput,
        ]);
        
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending_review,send_review_copy,rejected,approved_awaiting_delivery,stocked',
                'admin_notes' => 'nullable|string',
                'rev_book_id' => 'nullable|string|unique:books,rev_book_id,' . $book->id . ',id',
                'quantity' => 'nullable|integer|min:1', // Keep quantity validation
            ]);
            
            // Log the validated data
            Log::info('Book review data validated successfully', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'validated_data' => $validated,
                'admin_id' => $admin->id,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Book review validation failed', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'admin_id' => $admin->id,
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Book review validation error', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'exception' => $e->getMessage(),
                'request_data' => $request->all(),
                'admin_id' => $admin->id,
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error: ' . $e->getMessage()
                ], 422);
            }
            return back()->with('error', 'Validation error: ' . $e->getMessage());
        }
        
        try {
            $updated = $this->bookReviewService->reviewBook($book, $validated, $admin);
            
            Log::info('Book review service completed', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'updated' => $updated,
                'new_status' => $validated['status'] ?? null,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
            ]);
            
            if ($updated) {
                // Check if this was stocking and if ERPREV registration failed
                $erprevError = null;
                if ($validated['status'] === 'stocked') {
                    // Reload the book to check if rev_book_id was set
                    $book->refresh();
                    if (empty($book->rev_book_id)) {
                        $erprevError = 'Note: Book was stocked but could not be registered with ERPREV system. Please check system connectivity.';
                    }
                }
                
                Log::info('Book review successful', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'old_status' => $book->getOriginal('status'),
                    'new_status' => $validated['status'],
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'timestamp' => now()->toISOString(),
                ]);
                
                if ($request->expectsJson()) {
                    $response = [
                        'success' => true,
                        'message' => 'Book status updated successfully! Author has been notified.'
                    ];
                    if ($erprevError) {
                        $response['warning'] = $erprevError;
                    }
                    return response()->json($response);
                }
                
                $successMessage = 'Book status updated successfully! Author has been notified.';
                if ($erprevError) {
                    return back()->with('success', $successMessage)->with('warning', $erprevError);
                }
                return back()->with('success', $successMessage);
            } else {
                Log::warning('Book review failed - service returned false', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'validated_data' => $validated,
                    'admin_id' => $admin->id,
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update book status.'
                    ], 422);
                }
                return back()->with('error', 'Failed to update book status.');
            }
        } catch (\Exception $e) {
            Log::error('Book review process failed with exception', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'book_data' => $book->toArray(),
                'validated_data' => $validated ?? [],
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'timestamp' => now()->toISOString(),
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update book status: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Failed to update book status: ' . $e->getMessage());
        }
        
        Log::warning('Book review reached unexpected code path', [
            'book_id' => $book->id,
            'book_title' => $book->title,
            'validated_data' => $validated ?? [],
            'admin_id' => $admin->id,
        ]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unexpected error occurred during book review.'
            ], 500);
        }
        return back()->with('error', 'Unexpected error occurred during book review.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:pending_review,send_review_copy,reject,approve_delivery,stock,delete,restore,forceDelete',
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id',
            'admin_notes' => 'nullable|string',
        ]);

        $books = Book::withTrashed()->whereIn('id', $validated['book_ids'])->get();
        $successCount = 0;
        $admin = Auth::user();

        foreach ($books as $book) {
            $status = match($validated['action']) {
                'pending_review' => 'pending_review',
                'send_review_copy' => 'send_review_copy',
                'reject' => 'rejected',
                'approve_delivery' => 'approved_awaiting_delivery',
                'stock' => 'stocked',
                'delete' => null,
                'restore' => null,
                'forceDelete' => null,
            };

            switch ($validated['action']) {
                case 'delete':
                    if ($book->walletTransactions()->count() === 0) {
                        $book->delete();
                        $successCount++;
                    }
                    break;
                case 'restore':
                    if ($book->trashed()) {
                        $book->restore();
                        $successCount++;
                    }
                    break;
                case 'forceDelete':
                    if ($book->trashed()) {
                        // Check if there are any transactions before force deleting
                        if ($book->walletTransactions()->count() === 0) {
                            $book->forceDelete();
                            $successCount++;
                        }
                    }
                    break;
                default:
                    try {
                        $updated = $this->bookReviewService->reviewBook($book, [
                            'status' => $status,
                            'admin_notes' => $validated['admin_notes'] ?? null,
                        ], $admin);
                        
                        if ($updated) {
                            $successCount++;
                        }
                    } catch (\Exception $e) {
                        Log::error('Bulk action book review error: ' . $e->getMessage(), [
                            'exception' => $e,
                            'book_id' => $book->id,
                            'admin_id' => $admin->id,
                            'admin_name' => $admin->name,
                        ]);
                    }
                    break;
            }
        }

        Log::info('Bulk book action completed', [
            'action' => $validated['action'],
            'requested_count' => count($validated['book_ids']),
            'success_count' => $successCount,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$successCount} books."
        ]);
    }
    
    /**
     * View recent book review logs
     */
    public function reviewLogs()
    {
        // This would typically read from the log file or a dedicated logs table
        // For now, we'll just return a view that explains how to check logs
        return view('admin.books.logs');
    }

    public function exportCsv(Request $request)
    {
        // Get all books with filters applied
        $query = Book::with(['user', 'walletTransactions'])->whereNull('deleted_at');
        
        // Apply the same filters as in the index method
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        
        $books = $query->get();

        // Create CSV content
        $headers = ['Title', 'Author', 'Genre', 'Price', 'Status', 'Sales Count', 'Total Revenue', 'Submitted At'];
        $csvData = [];

        foreach ($books as $book) {
            $salesCount = $book->walletTransactions->where('type', 'sale')->count();
            $revenue = $book->walletTransactions->where('type', 'sale')->sum('amount');
            
            $csvData[] = [
                $book->title,
                $book->user->name,
                $book->genre,
                '₦' . number_format($book->price, 2),
                ucfirst(str_replace('_', ' ', $book->status)),
                $salesCount,
                '₦' . number_format($revenue, 2),
                $book->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Generate CSV
        $filename = 'books_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $callback = function() use ($headers, $csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        // Get all books with filters applied
        $query = Book::with(['user', 'walletTransactions'])->whereNull('deleted_at');
        
        // Apply the same filters as in the index method
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('genre', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        
        $books = $query->get();

        // Prepare data for the PDF
        $reportData = [
            'books' => $books,
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'filters' => [
                'status' => $request->status,
                'search' => $request->search,
                'genre' => $request->genre
            ]
        ];

        // Load the PDF view using the service container
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('admin.books.exports.pdf', $reportData);
        
        $filename = 'books_export_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}