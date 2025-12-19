<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payout;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class PayoutService
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Get payout overview for user
     */
    public function getPayoutOverview(User $user, array $filters = []): array
    {
        $payouts = $this->getPaginatedPayoutsByUser($user->id, $filters);
        $walletBalance = $user->getWalletBalance();
        $availableBalance = $this->walletService->getAvailableBalance($user);
        $payoutStats = $this->getPayoutStats($user->id);

        return [
            'payouts' => $payouts,
            'walletBalance' => $walletBalance,
            'availableBalance' => $availableBalance,
            'payoutStats' => $payoutStats,
        ];
    }

    /**
     * Create a new payout request
     */
    public function createPayoutRequest(User $user, array $data): Payout
    {
        // Validate payout eligibility
        $eligibility = $this->validatePayoutEligibility($user);
        
        if (!$eligibility['eligible']) {
            throw ValidationException::withMessages([
                'amount_requested' => $eligibility['reason']
            ]);
        }

        // Validate available balance
        $availableBalance = $this->walletService->getAvailableBalance($user);
        
        if ($data['amount_requested'] > $availableBalance) {
            $pendingPayouts = $this->getPendingPayoutsSum($user->id);
            throw ValidationException::withMessages([
                'amount_requested' => 'Insufficient balance. You have ₦' . number_format($pendingPayouts, 2) . ' in pending payouts.'
            ]);
        }

        // Validate minimum amount (₦300,000 as per new requirements)
        if ($data['amount_requested'] < 300000) {
            throw ValidationException::withMessages([
                'amount_requested' => 'Minimum payout amount is ₦300,000.00'
            ]);
        }

        $data['user_id'] = $user->id;
        $data['status'] = 'pending';

        return Payout::create($data);
    }

    /**
     * Validate payout eligibility based on new rules:
     * - Available balance must be at least ₦300,000
     * - Minimum of 30 days must have elapsed since the last eligible transaction or payout
     */
    public function validatePayoutEligibility(User $user): array
    {
        // Check available balance
        $availableBalance = $this->walletService->getAvailableBalance($user);
        
        if ($availableBalance < 300000) {
            return [
                'eligible' => false,
                'reason' => 'Your available balance must be at least ₦300,000.00 to request a payout.'
            ];
        }

        // Check if 30 days have elapsed since last eligible transaction
        $lastEligibleTransaction = $this->getLastEligibleTransactionDate($user);
        
        if ($lastEligibleTransaction) {
            $daysSinceLastTransaction = $lastEligibleTransaction->diffInDays(now());
            
            if ($daysSinceLastTransaction < 30) {
                $daysRemaining = 30 - $daysSinceLastTransaction;
                return [
                    'eligible' => false,
                    'reason' => "You must wait {$daysRemaining} more day(s) before requesting another payout. Payouts can only be requested once every 30 days."
                ];
            }
        }

        // Check if 30 days have elapsed since last approved payout
        $lastApprovedPayout = $this->getLastApprovedPayoutDate($user);
        
        if ($lastApprovedPayout) {
            $daysSinceLastPayout = $lastApprovedPayout->diffInDays(now());
            
            if ($daysSinceLastPayout < 30) {
                $daysRemaining = 30 - $daysSinceLastPayout;
                return [
                    'eligible' => false,
                    'reason' => "You must wait {$daysRemaining} more day(s) before requesting another payout. Payouts can only be requested once every 30 days."
                ];
            }
        }

        return [
            'eligible' => true,
            'reason' => 'Eligible for payout'
        ];
    }

    /**
     * Get the date of the last eligible transaction (sale) for the user
     */
    private function getLastEligibleTransactionDate(User $user)
    {
        return $user->walletTransactions()
            ->where('type', 'sale')
            ->orderBy('created_at', 'desc')
            ->first()?->created_at;
    }

    /**
     * Get the date of the last approved payout for the user
     */
    private function getLastApprovedPayoutDate(User $user)
    {
        return $user->payouts()
            ->where('status', 'approved')
            ->orderBy('processed_at', 'desc')
            ->first()?->processed_at;
    }

    /**
     * Calculate payout fee
     */
    public function calculatePayoutFee(float $amount): array
    {
        $feePercentage = 2.5; // 2.5%
        $fee = ($amount * $feePercentage) / 100;
        $netAmount = $amount - $fee;

        return [
            'gross_amount' => $amount,
            'fee_percentage' => $feePercentage,
            'fee_amount' => $fee,
            'net_amount' => $netAmount,
        ];
    }

    /**
     * Update payout status (admin function)
     */
    public function updatePayoutStatus(Payout $payout, string $status, ?string $adminNotes = null): bool
    {
        if (!in_array($status, ['pending', 'approved', 'denied'])) {
            throw new \InvalidArgumentException('Invalid payout status');
        }

        $data = ['status' => $status];
        
        // Set processed timestamp when approving
        if ($status === 'approved') {
            $data['processed_at'] = now();
        }
        
        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }

        return $payout->update($data);
    }

    /**
     * Get all pending payouts (admin function)
     */
    public function getAllPendingPayouts()
    {
        return Payout::where('status', 'pending')->get();
    }

    /**
     * Update user payment details
     */
    public function updatePaymentDetails(User $user, array $paymentDetails): bool
    {
        // Validate payment method specific fields
        $this->validatePaymentDetails($paymentDetails);

        return $user->update(['payment_details' => $paymentDetails]);
    }

    /**
     * Validate payment details based on payment method
     */
    private function validatePaymentDetails(array $paymentDetails): void
    {
        $method = $paymentDetails['payment_method'] ?? null;

        if (!$method) {
            throw ValidationException::withMessages([
                'payment_method' => 'Payment method is required'
            ]);
        }

        switch ($method) {
            case 'bank_transfer':
                if (empty($paymentDetails['bank_name']) || 
                    empty($paymentDetails['account_number'])) {
                    throw ValidationException::withMessages([
                        'bank_details' => 'Bank transfer requires bank name and account number'
                    ]);
                }
                break;
            case 'paypal':
                if (empty($paymentDetails['paypal_email'])) {
                    throw ValidationException::withMessages([
                        'paypal_email' => 'PayPal requires email address'
                    ]);
                }
                break;
            case 'stripe':
                if (empty($paymentDetails['stripe_account_id'])) {
                    throw ValidationException::withMessages([
                        'stripe_account_id' => 'Stripe requires account ID'
                    ]);
                }
                break;
            default:
                throw ValidationException::withMessages([
                    'payment_method' => 'Invalid payment method selected'
                ]);
        }
    }

    /**
     * Get total payouts for user
     */
    public function getTotalPayouts(int $userId): float
    {
        return Payout::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount_requested');
    }

    /**
     * Get pending payouts for user
     */
    public function getPendingPayouts(int $userId): float
    {
        return $this->getPendingPayoutsSum($userId);
    }

    /**
     * Get completed payouts for user
     */
    public function getCompletedPayouts(int $userId): float
    {
        return Payout::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount_requested');
    }

    /**
     * Get recent payouts for user
     */
    public function getRecentPayouts(int $userId, int $limit = 3): Collection
    {
        return Payout::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get paginated payouts by user
     */
    public function getPaginatedPayoutsByUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Payout::where('user_id', $userId)
            ->with(['user'])
            ->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
     * Get payout stats for user
     */
    public function getPayoutStats(int $userId): array
    {
        $totalPayouts = Payout::where('user_id', $userId)->count();
        $pendingPayouts = Payout::where('user_id', $userId)->where('status', 'pending')->count();
        $approvedPayouts = Payout::where('user_id', $userId)->where('status', 'approved')->count();
        $deniedPayouts = Payout::where('user_id', $userId)->where('status', 'denied')->count();
        $pendingAmount = Payout::where('user_id', $userId)->where('status', 'pending')->sum('amount_requested');

        return [
            'total' => $totalPayouts,
            'pending' => $pendingPayouts,
            'approved' => $approvedPayouts,
            'denied' => $deniedPayouts,
            'pending_amount' => $pendingAmount,
        ];
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
}