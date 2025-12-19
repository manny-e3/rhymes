<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\WalletTransaction;
use App\Models\Payout;
use Carbon\Carbon;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:author']);
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        // Get book statistics
        $totalBooks = Book::where('user_id', $user->id)->count();
        $pendingBooks = Book::where('user_id', $user->id)->where('status', 'pending')->count();
        $publishedBooks = Book::where('user_id', $user->id)->whereIn('status', ['accepted', 'stocked'])->count();
        $rejectedBooks = Book::where('user_id', $user->id)->where('status', 'rejected')->count();

        // Get wallet statistics
        $totalEarnings = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'sale')
            ->sum('amount');
            
        $monthlyEarnings = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'sale')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('amount');
            
        $previousMonthEarnings = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'sale')
            ->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])
            ->sum('amount');
            
        $monthlyGrowth = $previousMonthEarnings > 0 
            ? (($monthlyEarnings - $previousMonthEarnings) / $previousMonthEarnings) * 100 
            : 0;

        // Get payout statistics
        $totalPayouts = Payout::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount_requested');
            
        $pendingPayouts = Payout::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount_requested');

        // Calculate available balance (earnings minus payouts)
        $availableBalance = $totalEarnings - $totalPayouts - $pendingPayouts;

        // Get recent data
        $recentBooks = Book::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
            
        $recentTransactions = WalletTransaction::where('user_id', $user->id)
            ->with('book')
            ->latest()
            ->limit(10)
            ->get();
            
        $recentPayouts = Payout::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Get book sales analytics
        $bookSales = Book::where('user_id', $user->id)
            ->whereIn('status', ['accepted', 'stocked'])
            ->withCount(['walletTransactions as sales_count' => function($query) {
                $query->where('type', 'sale');
            }])
            ->withSum(['walletTransactions as total_revenue' => function($query) {
                $query->where('type', 'sale');
            }], 'amount')
            ->having('sales_count', '>', 0)
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(function($book) {
                return [
                    'title' => $book->title,
                    'sales_count' => $book->sales_count,
                    'total_revenue' => $book->total_revenue ?? 0,
                    'avg_price' => $book->sales_count > 0 ? ($book->total_revenue ?? 0) / $book->sales_count : 0,
                ];
            });

        $analytics = [
            'user' => $user,
            'stats' => [
                'total_books' => $totalBooks,
                'pending_books' => $pendingBooks,
                'published_books' => $publishedBooks,
                'rejected_books' => $rejectedBooks,
                'wallet_balance' => $totalEarnings,
                'available_balance' => max(0, $availableBalance),
                'total_earnings' => $totalEarnings,
                'monthly_earnings' => $monthlyEarnings,
                'monthly_growth' => round($monthlyGrowth, 1),
                'pending_payouts' => $pendingPayouts,
                'total_payouts' => $totalPayouts,
            ],
            'analytics' => [
                'book_sales' => $bookSales,
            ],
            'recent' => [
                'books' => $recentBooks,
                'transactions' => $recentTransactions,
                'payouts' => $recentPayouts,
            ],
        ];

        return view('author.dashboard', compact('analytics'));
    }
}
