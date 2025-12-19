<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function salesDashboard()
    {
        // Get data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        // Get sales metrics
        $totalRevenue = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $totalSales = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $avgOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate);
        
        // Get previous period data for comparison
        $prevStartDate = Carbon::now()->subDays(60);
        $prevEndDate = Carbon::now()->subDays(30);
        
        $prevRevenue = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->sum('amount');
            
        $prevSales = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->count();
            
        $prevAvgOrderValue = $prevSales > 0 ? $prevRevenue / $prevSales : 0;
        
        // Calculate changes
        $revenueChange = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : ($totalRevenue > 0 ? 100 : 0);
        $salesChange = $prevSales > 0 ? (($totalSales - $prevSales) / $prevSales) * 100 : ($totalSales > 0 ? 100 : 0);
        $aovChange = $prevAvgOrderValue > 0 ? (($avgOrderValue - $prevAvgOrderValue) / $prevAvgOrderValue) * 100 : ($avgOrderValue > 0 ? 100 : 0);
        
        // Get top selling books
        $topBooks = Book::select('books.title')
            ->selectRaw('COUNT(wallet_transactions.id) as sales_count')
            ->selectRaw('SUM(wallet_transactions.amount) as total_revenue')
            ->leftJoin('wallet_transactions', function($join) use ($startDate, $endDate) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale')
                     ->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
            })
            ->groupBy('books.title')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
            
        // Get genre performance
        $genreData = Book::select('genre')
            ->selectRaw('SUM(wallet_transactions.amount) as revenue')
            ->leftJoin('wallet_transactions', function($join) use ($startDate, $endDate) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale')
                     ->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
            })
            ->whereNotNull('genre')
            ->groupBy('genre')
            ->orderByDesc('revenue')
            ->get();
        
        return view('admin.reports.sales-dashboard', compact(
            'totalRevenue',
            'totalSales',
            'avgOrderValue',
            'revenueChange',
            'salesChange',
            'aovChange',
            'chartData',
            'topBooks',
            'genreData'
        ));
    }

    public function sales(Request $request)
    {
        $period = $request->get('period', 'last_30_days');
        $startDate = null;
        $endDate = null;

        // Calculate date range based on period
        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7);
                $endDate = Carbon::now();
                break;
            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now();
                break;
            case 'custom':
                $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
                $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
                break;
            default:
                $startDate = Carbon::now()->subDays(30);
                $endDate = Carbon::now();
        }

        // Get metrics for current period
        $currentMetrics = $this->getSalesMetrics($startDate, $endDate);
        
        // Get metrics for previous period (for comparison)
        $periodLength = $startDate->diffInDays($endDate);
        $prevStartDate = $startDate->copy()->subDays($periodLength);
        $prevEndDate = $startDate->copy()->subDay();
        $previousMetrics = $this->getSalesMetrics($prevStartDate, $prevEndDate);

        // Calculate percentage changes
        $metrics = [
            'total_revenue' => $currentMetrics['total_revenue'],
            'total_sales' => $currentMetrics['total_sales'],
            'avg_order_value' => $currentMetrics['avg_order_value'],
            'platform_commission' => $currentMetrics['platform_commission'],
            'commission_rate' => 15, // This should come from settings
            'revenue_change' => $this->calculatePercentageChange($previousMetrics['total_revenue'], $currentMetrics['total_revenue']),
            'sales_change' => $this->calculatePercentageChange($previousMetrics['total_sales'], $currentMetrics['total_sales']),
            'aov_change' => $this->calculatePercentageChange($previousMetrics['avg_order_value'], $currentMetrics['avg_order_value']),
        ];

        // Get chart data
        $chartData = $this->getChartData($startDate, $endDate);

        // Get top performing books
        $topBooks = Book::select([
                'books.id',
                'books.title',
                'books.user_id',
                'books.price',
                'books.genre',
                'books.status',
                'books.created_at'
            ])
            ->selectRaw('COUNT(wallet_transactions.id) as sales_count')
            ->selectRaw('SUM(wallet_transactions.amount) as total_revenue')
            ->leftJoin('wallet_transactions', function($join) use ($startDate, $endDate) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale')
                     ->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
            })
            ->with('user')
            ->groupBy([
                'books.id',
                'books.title',
                'books.user_id',
                'books.price',
                'books.genre',
                'books.status',
                'books.created_at'
            ])
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Get all books for filter
        $allBooks = Book::select('id', 'title')->get();

        // Get transactions
        $transactionsQuery = WalletTransaction::with(['book', 'user'])
            ->where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('book_id')) {
            $transactionsQuery->where('book_id', $request->book_id);
        }

        $transactions = $transactionsQuery->latest()->paginate(20);

        // Ensure we have default values if no data exists
        if ($topBooks->isEmpty()) {
            $topBooks = collect([]);
        }
        
        if ($transactions->isEmpty()) {
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('admin.reports.sales', compact(
            'metrics', 
            'chartData', 
            'topBooks', 
            'allBooks', 
            'transactions'
        ));
    }

    public function analytics(Request $request)
    {
        $period = $request->get('period', 30);
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // Get analytics data
        $analytics = [
            'active_users' => User::where('last_login_at', '>=', $startDate)->count(),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'book_views' => $this->getBookViews($startDate, $endDate),
            'views_change' => 5.2, // This would come from actual view tracking
            'conversion_rate' => $this->getConversionRate($startDate, $endDate),
            'author_retention' => $this->getAuthorRetention($startDate, $endDate),
            'avg_session_duration' => 12, // This would come from analytics service
            'pages_per_session' => 3.4,
            'bounce_rate' => 45.2,
            'return_rate' => 32.1,
            'gross_revenue' => WalletTransaction::where('type', 'sale')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'platform_revenue' => WalletTransaction::where('type', 'sale')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') * 0.15,
            'author_earnings' => WalletTransaction::where('type', 'sale')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') * 0.85,
            'payouts_paid' => WalletTransaction::where('type', 'payout')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
        ];

        // Get chart data for user growth
        $chartData = $this->getAnalyticsChartData($startDate, $endDate);

        // Get genre performance data
        $genreData = $this->getGenrePerformance($startDate, $endDate);

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
            ->limit(10)
            ->get();

        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        return view('admin.reports.analytics', compact(
            'analytics',
            'chartData',
            'genreData',
            'topAuthors',
            'recentActivity'
        ));
    }

    private function getSalesMetrics($startDate, $endDate)
    {
        $totalRevenue = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $totalSales = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $avgOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        $platformCommission = $totalRevenue * 0.15; // 15% commission

        return [
            'total_revenue' => $totalRevenue,
            'total_sales' => $totalSales,
            'avg_order_value' => $avgOrderValue,
            'platform_commission' => $platformCommission,
        ];
    }

    private function calculatePercentageChange($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    private function getChartData($startDate, $endDate)
    {
        $days = [];
        $revenue = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dayRevenue = WalletTransaction::where('type', 'sale')
                ->whereDate('created_at', $currentDate)
                ->sum('amount');
            
            $days[] = $currentDate->format('M d');
            $revenue[] = $dayRevenue;
            
            $currentDate->addDay();
        }

        return [
            'labels' => $days,
            'revenue' => $revenue,
        ];
    }

    private function getAnalyticsChartData($startDate, $endDate)
    {
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

        return [
            'labels' => $days,
            'users' => $users,
            'authors' => $authors,
            'books' => $books,
        ];
    }

    private function getGenrePerformance($startDate, $endDate)
    {
        $genreData = Book::select('genre')
            ->selectRaw('SUM(wallet_transactions.amount) as revenue')
            ->leftJoin('wallet_transactions', function($join) use ($startDate, $endDate) {
                $join->on('books.id', '=', 'wallet_transactions.book_id')
                     ->where('wallet_transactions.type', '=', 'sale')
                     ->whereBetween('wallet_transactions.created_at', [$startDate, $endDate]);
            })
            ->groupBy('genre')
            ->orderByDesc('revenue')
            ->get();

        return [
            'labels' => $genreData->pluck('genre')->toArray(),
            'data' => $genreData->pluck('revenue')->toArray(),
        ];
    }

    private function getBookViews($startDate, $endDate)
    {
        // This would typically come from a page views tracking system
        // For now, return a mock value
        return rand(5000, 15000);
    }

    private function getConversionRate($startDate, $endDate)
    {
        $views = $this->getBookViews($startDate, $endDate);
        $sales = WalletTransaction::where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return $views > 0 ? ($sales / $views) * 100 : 0;
    }

    private function getAuthorRetention($startDate, $endDate)
    {
        $totalAuthors = User::role('author')->count();
        $activeAuthors = User::role('author')
            ->whereHas('books', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

        return $totalAuthors > 0 ? ($activeAuthors / $totalAuthors) * 100 : 0;
    }

    private function getRecentActivity()
    {
        $activities = [];

        // Recent user registrations
        $recentUsers = User::latest()->limit(3)->get();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'type' => 'user_registered',
                'description' => "New user \"{$user->name}\" registered",
                'time' => $user->created_at->diffForHumans(),
            ];
        }

        // Recent book publications
        $recentBooks = Book::where('status', 'accepted')->latest()->limit(2)->get();
        foreach ($recentBooks as $book) {
            $activities[] = [
                'type' => 'book_published',
                'description' => "Book \"{$book->title}\" was published",
                'time' => $book->updated_at->diffForHumans(),
            ];
        }

        // Sort by time and return
        return collect($activities)->sortByDesc('time')->take(5)->values()->toArray();
    }
}