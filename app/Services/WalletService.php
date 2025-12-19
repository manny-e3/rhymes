<?php

namespace App\Services;

use App\Models\WalletTransaction;
use App\Models\Payout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WalletService
{
    /**
     * Get wallet overview data for user
     */
    public function getWalletOverview(User $user, array $filters = []): array
    {
        $balance = $user->getWalletBalance();
        $transactions = $this->getPaginatedTransactionsByUser($user->id, $filters);
        $salesByBook = $this->getSalesByBookForUser($user->id);
        $analytics = $this->getWalletAnalytics($user);

        return [
            'balance' => $balance,
            'transactions' => $transactions,
            'salesByBook' => $salesByBook,
            'analytics' => $analytics,
        ];
    }

    /**
     * Get wallet analytics for user
     */
    public function getWalletAnalytics(User $user): array
    {
        return [
            'total_earnings' => $this->getTotalEarnings($user->id),
            'this_month_earnings' => $this->getEarningsForPeriod($user->id, 'this_month'),
            'last_month_earnings' => $this->getEarningsForPeriod($user->id, 'last_month'),
            'total_sales_count' => $this->getSalesCount($user->id),
            'this_month_sales_count' => $this->getSalesCountForPeriod($user->id, 'this_month'),
            'average_sale_amount' => $this->getAverageSaleAmount($user->id),
            'total_payouts' => $this->getTotalPayouts($user->id),
        ];
    }

    /**
     * Get available balance for user (considering pending payouts)
     */
    public function getAvailableBalance(User $user): float
    {
        $walletBalance = $user->getWalletBalance();
        $pendingPayouts = $this->getPendingPayoutsSum($user->id);
        
        return $walletBalance - $pendingPayouts;
    }

    /**
     * Export wallet transactions
     */
    public function exportTransactions(User $user, array $filters = []): array
    {
        $transactions = $this->getTransactionsForExport($user->id, $filters);
        
        $csvData = [];
        foreach ($transactions as $transaction) {
            $csvData[] = [
                'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                'type' => ucfirst($transaction->type),
                'book' => $transaction->book ? $transaction->book->title : 'N/A',
                'amount' => '₦' . number_format($transaction->amount, 2),
                'description' => $transaction->meta['description'] ?? ''
            ];
        }

        return [
            'filename' => 'wallet_transactions_' . now()->format('Y-m-d') . '.csv',
            'headers' => ['Date', 'Type', 'Book', 'Amount', 'Description'],
            'data' => $csvData
        ];
    }

    /**
     * Create a wallet transaction
     */
    public function createTransaction(array $data): void
    {
        WalletTransaction::create($data);
    }

    /**
     * Get wallet balance for user ID
     */
    public function getWalletBalance(int $userId): float
    {
        $user = User::find($userId);
        return $user ? $user->getWalletBalance() : 0.0;
    }

    /**
     * Get available balance for user ID
     */
    public function getAvailableBalanceById(int $userId): float
    {
        $user = User::find($userId);
        return $user ? $this->getAvailableBalance($user) : 0.0;
    }

    /**
     * Get total earnings for user
     */
    public function getTotalEarnings(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->sum('amount');
    }

    /**
     * Get monthly earnings for user
     */
    public function getMonthlyEarnings(int $userId): float
    {
        return $this->getEarningsForPeriod($userId, 'this_month');
    }

    /**
     * Get weekly earnings for user
     */
    public function getWeeklyEarnings(int $userId): float
    {
        return $this->getEarningsForPeriod($userId, 'this_week');
    }

    /**
     * Get recent transactions for user
     */
    public function getRecentTransactions(int $userId, int $limit = 5): Collection
    {
        return WalletTransaction::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get book sales analytics for user
     */
    public function getBookSalesAnalytics(int $userId): array
    {
        return $this->getSalesByBookForUser($userId);
    }

    /**
     * Get monthly earnings chart data
     */
    public function getMonthlyEarningsChart(int $userId, int $months = 6): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $earnings = $this->getEarningsForDateRange($userId, $startOfMonth, $endOfMonth);
            
            $data[] = [
                'month' => $date->format('M Y'),
                'earnings' => $earnings
            ];
        }
        
        return $data;
    }

    /**
     * Get earnings for specific period
     */
    public function getEarningsForPeriod(int $userId, string $period): float
    {
        $query = WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale');

        switch ($period) {
            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
                break;
            case 'this_week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
        }

        return $query->sum('amount');
    }

    /**
     * Get earnings for date range
     */
    public function getEarningsForDateRange(int $userId, Carbon $startDate, Carbon $endDate): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Get paginated transactions by user
     */
    public function getPaginatedTransactionsByUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = WalletTransaction::where('user_id', $userId)
            ->with(['book', 'user'])
            ->latest();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get sales by book for user
     */
    public function getSalesByBookForUser(int $userId): array
    {
        return WalletTransaction::where('wallet_transactions.user_id', $userId)
            ->where('type', 'sale')
            ->join('books', 'wallet_transactions.book_id', '=', 'books.id')
            ->selectRaw('books.title, SUM(wallet_transactions.amount) as total_sales, COUNT(*) as sales_count')
            ->groupBy('books.title')
            ->get()
            ->toArray();
    }

    /**
     * Get sales count for user
     */
    public function getSalesCount(int $userId): int
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->count();
    }

    /**
     * Get sales count for period
     */
    public function getSalesCountForPeriod(int $userId, string $period): int
    {
        $query = WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale');

        switch ($period) {
            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)
                    ->whereYear('created_at', now()->subMonth()->year);
                break;
            case 'this_week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
        }

        return $query->count();
    }

    /**
     * Get average sale amount for user
     */
    public function getAverageSaleAmount(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'sale')
            ->avg('amount') ?? 0.0;
    }

    /**
     * Get total payouts for user
     */
    public function getTotalPayouts(int $userId): float
    {
        return WalletTransaction::where('user_id', $userId)
            ->where('type', 'payout')
            ->sum('amount');
    }

    /**
     * Get pending payouts sum for user
     */
    public function getPendingPayoutsSum(int $userId): float
    {
        return Payout::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount_requested');
    }

    /**
     * Get transactions for export
     */
    public function getTransactionsForExport(int $userId, array $filters = []): Collection
    {
        $query = WalletTransaction::where('user_id', $userId)
            ->with(['book'])
            ->latest();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }
}