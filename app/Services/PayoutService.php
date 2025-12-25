<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payout;
use App\Models\Setting;
use App\Notifications\PayoutRequested;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

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

        // Validate minimum amount from settings
        $minPayoutAmount = $this->getSetting('min_payout_amount', 300000);
        if ($data['amount_requested'] < $minPayoutAmount) {
            throw ValidationException::withMessages([
                'amount_requested' => 'Minimum payout amount is ₦' . number_format($minPayoutAmount, 2)
            ]);
        }

        $data['user_id'] = $user->id;
        $data['status'] = 'pending';

        $payout = Payout::create($data);
        
        // Notify all admins about the new payout request
        $this->notifyAdminsAboutNewPayout($payout, $user);
        
        return $payout;
    }

    /**
     * Notify all admins about a new payout request
     */
    private function notifyAdminsAboutNewPayout(Payout $payout, User $author): void
    {
        try {
            // Get all admins
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();
            
            Log::info('Notifying admins about new payout request', [
                'payout_id' => $payout->id,
                'amount_requested' => $payout->amount_requested,
                'author_id' => $author->id,
                'author_name' => $author->name,
                'admin_count' => $admins->count()
            ]);
            
            // Notify each admin
            foreach ($admins as $admin) {
                try {
                    $admin->notify(new PayoutRequested($payout));
                    Log::info('Payout request notification sent to admin', [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'admin_email' => $admin->email,
                        'payout_id' => $payout->id,
                        'amount_requested' => $payout->amount_requested
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send payout request notification to admin', [
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->name,
                        'admin_email' => $admin->email,
                        'payout_id' => $payout->id,
                        'amount_requested' => $payout->amount_requested,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about new payout request', [
                'payout_id' => $payout->id,
                'amount_requested' => $payout->amount_requested,
                'author_id' => $author->id,
                'author_name' => $author->name,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate payout eligibility based on configurable rules
     */
    public function validatePayoutEligibility(User $user): array
    {
        // Check available balance from settings
        $minPayoutAmount = $this->getSetting('min_payout_amount', 300000);
        $availableBalance = $this->walletService->getAvailableBalance($user);
        
        if ($availableBalance < $minPayoutAmount) {
            return [
                'eligible' => false,
                'reason' => 'Your available balance must be at least ₦' . number_format($minPayoutAmount, 2) . ' to request a payout.'
            ];
        }

        // Check frequency limit from settings
        $frequencyDays = $this->getSetting('payout_frequency_days', 1);
        
        // Check if required days have elapsed since last eligible transaction
        $lastEligibleTransaction = $this->getLastEligibleTransactionDate($user);
        
        if ($lastEligibleTransaction) {
            $daysSinceLastTransaction = $lastEligibleTransaction->diffInDays(now());
            
            if ($daysSinceLastTransaction < $frequencyDays) {
                $daysRemaining = $frequencyDays - $daysSinceLastTransaction;
                return [
                    'eligible' => false,
                    'reason' => "You must wait {$daysRemaining} more day(s) before requesting another payout. Payouts can only be requested once every {$frequencyDays} days."
                ];
            }
        }

        // Check if required days have elapsed since last approved payout
        $lastApprovedPayout = $this->getLastApprovedPayoutDate($user);
        
        if ($lastApprovedPayout) {
            $daysSinceLastPayout = $lastApprovedPayout->diffInDays(now());
            
            if ($daysSinceLastPayout < $frequencyDays) {
                $daysRemaining = $frequencyDays - $daysSinceLastPayout;
                return [
                    'eligible' => false,
                    'reason' => "You must wait {$daysRemaining} more day(s) before requesting another payout. Payouts can only be requested once every {$frequencyDays} days."
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
        $feePercentage = $this->getSetting('payout_fee', 2.5); // Get from settings
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
        return $user->update(['payment_details' => $paymentDetails]);
    }

    /**
     * Get paginated payouts for a user
     */
    public function getPaginatedPayoutsByUser(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Payout::where('user_id', $userId)->latest();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Get payout statistics for a user
     */
    public function getPayoutStats(int $userId): array
    {
        return [
            'total' => Payout::where('user_id', $userId)->count(),
            'pending' => Payout::where('user_id', $userId)->where('status', 'pending')->count(),
            'approved' => Payout::where('user_id', $userId)->where('status', 'approved')->count(),
            'denied' => Payout::where('user_id', $userId)->where('status', 'denied')->count(),
        ];
    }

    /**
     * Get sum of pending payouts for a user
     */
    public function getPendingPayoutsSum(int $userId): float
    {
        return Payout::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount_requested');
    }

    /**
     * Get formatted payout information for display
     */
    public function getPayoutInformation(): array
    {
        return [
            'minimum_amount' => $this->getSetting('min_payout_amount', 300000),
            'processing_time_min' => $this->getSetting('payout_processing_time_min', 3),
            'processing_time_max' => $this->getSetting('payout_processing_time_max', 5),
            'frequency_days' => $this->getSetting('payout_frequency_days', 30),
            'fee_percentage' => $this->getSetting('payout_fee', 2.5),
        ];
    }

    /**
     * Get setting value from database or fallback to default
     */
    private function getSetting(string $key, $default = null)
    {
        $value = Setting::get($key, $default);
        return $value !== null ? $value : $default;
    }
}