<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Payout;
use App\Models\WalletTransaction;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function dashboard()
    {
        // Get stats for the dashboard
        $totalUsers = User::count();
        $totalAuthors = User::role('author')->count();
        $totalBooks = Book::count();
        $publishedBooks = Book::where('status', 'accepted')->count();
        $pendingBooks = Book::where('status', 'pending')->count();
        
        // Revenue calculations
        $totalRevenue = WalletTransaction::where('type', 'sale')->sum('amount');
        $thisMonthRevenue = WalletTransaction::where('type', 'sale')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
            
        $lastMonthRevenue = WalletTransaction::where('type', 'sale')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount');
            
        $revenueGrowth = $lastMonthRevenue > 0 ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        
        // Payout calculations
        $pendingPayouts = Payout::where('status', 'pending')->count();
        $pendingPayoutAmount = Payout::where('status', 'pending')->sum('amount_requested');
        $approvedPayouts = Payout::where('status', 'approved')->count();
        $totalPayoutAmount = Payout::where('status', 'approved')->sum('amount_requested');
        
        $analytics = [
            'stats' => [
                'total_users' => $totalUsers,
                'total_authors' => $totalAuthors,
                'total_books' => $totalBooks,
                'published_books' => $publishedBooks,
                'pending_books' => $pendingBooks,
                'total_revenue' => $totalRevenue,
                'this_month_revenue' => $thisMonthRevenue,
                'last_month_revenue' => $lastMonthRevenue,
                'revenue_growth' => $revenueGrowth,
                'pending_payouts' => $pendingPayouts,
                'pending_payout_amount' => $pendingPayoutAmount,
                'approved_payouts' => $approvedPayouts,
                'total_payout_amount' => $totalPayoutAmount,
            ],
            'recent' => [
                'books' => Book::with('user')->latest()->limit(5)->get(),
                'users' => User::latest()->limit(5)->get(),
            ]
        ];
        
        return view('admin.dashboard', compact('analytics'));
    }

    public function unifiedDashboard(Request $request)
    {
        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date') ? Carbon::createFromFormat('m/d/Y', $request->get('start_date'))->startOfDay() : Carbon::now()->subDays(30);
        $endDate = $request->get('end_date') ? Carbon::createFromFormat('m/d/Y', $request->get('end_date'))->endOfDay() : Carbon::now();
        
        // Get overview stats
        $totalUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeUsers = User::where('last_login_at', '>=', $startDate)->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Revenue calculations
        $grossRevenue = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $platformRevenue = $grossRevenue * 0.15; // 15% commission
        $authorEarnings = $grossRevenue * 0.85; // 85% to authors
        
        // Payout calculations
        $payoutsPaid = WalletTransaction::where('type', 'payout')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $overview = [
            'stats' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'new_users' => $newUsers,
                'gross_revenue' => $grossRevenue,
                'platform_revenue' => $platformRevenue,
                'author_earnings' => $authorEarnings,
                'payouts_paid' => $payoutsPaid,
            ]
        ];
        
        // Get chart data for user growth
        $days = [];
        $users = [];
        $authors = [];
        $books = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dayUsers = User::whereDate('created_at', $currentDate)->count();
            $dayAuthors = User::role('author')->whereDate('created_at', $currentDate)->count();
            $dayBooks = Book::whereDate('created_at', $currentDate)->count();
            
            $days[] = $currentDate->format('M d');
            $users[] = $dayUsers;
            $authors[] = $dayAuthors;
            $books[] = $dayBooks;
            
            $currentDate->addDay();
        }
        
        $analytics = [
            'chartData' => [
                'labels' => $days,
                'users' => $users,
                'authors' => $authors,
                'books' => $books,
            ]
        ];
        
        // Get sales metrics
        $totalRevenue = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $totalSales = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $avgOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        $sales = [
            'metrics' => [
                'total_revenue' => $totalRevenue,
                'total_sales' => $totalSales,
                'avg_order_value' => $avgOrderValue,
            ]
        ];
        
        // Get top authors
        $topAuthors = User::select([
                'users.id',
                'users.name',
                'users.email',
                'users.created_at'
            ])
            ->selectRaw('SUM(wallet_transactions.amount) as total_earnings')
            ->selectRaw('COUNT(DISTINCT books.id) as books_count')
            ->join('books', 'users.id', '=', 'books.user_id')
            ->leftJoin('wallet_transactions', function($join) use ($startDate, $endDate) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale')
                     ->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
            })
            ->role('author')
            ->groupBy([
                'users.id',
                'users.name',
                'users.email',
                'users.created_at'
            ])
            ->orderByDesc('total_earnings')
            ->limit(5)
            ->get();
        
        // Get top books
        $topBooks = Book::select([
                'books.id',
                'books.title',
                'books.genre'
            ])
            ->selectRaw('SUM(wallet_transactions.amount) as total_revenue')
            ->join('users', 'books.user_id', '=', 'users.id')
            ->leftJoin('wallet_transactions', function($join) use ($startDate, $endDate) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale')
                     ->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
            })
            ->groupBy([
                'books.id',
                'books.title',
                'books.genre'
            ])
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
        
        return view('admin.unified-dashboard', compact('overview', 'analytics', 'sales', 'topAuthors', 'topBooks'));
    }

    public function userActivity(Request $request)
    {
        // Get the period from request or default to 30 days
        $period = $request->get('period', 30);
        
        // Calculate the start date based on period
        $startDate = now()->subDays($period);
        
        // Fetch activities from the database
        $activitiesQuery = \App\Models\UserActivity::with('user')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc');
        
        // Paginate the results (15 per page)
        $paginatedActivities = $activitiesQuery->paginate(15)->appends(['period' => $period]);
        
        // Pass the paginated activities to the view
        return view('admin.users.activity', compact('paginatedActivities'));
    }

    // Test method for SweetAlert messages
    public function testSweetAlert(Request $request)
    {
        $type = $request->query('type', 'success');
        
        switch ($type) {
            case 'error':
                return redirect()->route('admin.dashboard')->with('error', 'This is a test error message!');
            case 'warning':
                return redirect()->route('admin.dashboard')->with('warning', 'This is a test warning message!');
            case 'info':
                return redirect()->route('admin.dashboard')->with('info', 'This is a test info message!');
            default:
                return redirect()->route('admin.dashboard')->with('success', 'This is a test success message!');
        }
    }
}