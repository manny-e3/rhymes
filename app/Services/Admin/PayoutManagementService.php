<?php

namespace App\Services\Admin;

use App\Models\Payout;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Notifications\PayoutStatusChanged;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PayoutManagementService
{
    /**
     * Get paginated payouts for admin review
     */
    public function getPayoutsForReview(): LengthAwarePaginator
    {
        return Payout::with('user')->latest()->paginate(15);
    }

    /**
     * Approve a payout request
     */
    public function approvePayout(Payout $payout, ?string $adminNotes, User $admin): bool
    {
        if ($payout->status !== 'pending') {
            throw new \InvalidArgumentException('Only pending payouts can be approved');
        }

        $oldStatus = $payout->status;
        
        // Update payout status
        $updated = $payout->update([
            'status' => 'approved',
            'admin_notes' => $adminNotes
        ]);
        
        if ($updated) {
            // Update processed timestamp
            $payout->update(['processed_at' => now()]);
            
            // Create negative wallet transaction for payout
            $this->createPayoutTransaction($payout, $admin);
            
            // Send notification
            $payout->user->notify(new PayoutStatusChanged($payout, $oldStatus, 'approved'));
        }
        
        return $updated;
    }

    /**
     * Deny a payout request
     */
    public function denyPayout(Payout $payout, string $adminNotes, User $admin): bool
    {
        if ($payout->status !== 'pending') {
            throw new \InvalidArgumentException('Only pending payouts can be denied');
        }

        if (empty($adminNotes)) {
            throw new \InvalidArgumentException('Admin notes are required when denying a payout');
        }

        $oldStatus = $payout->status;
        
        // Update payout status
        $updated = $payout->update([
            'status' => 'denied',
            'admin_notes' => $adminNotes
        ]);
        
        if ($updated) {
            // Update processed timestamp
            $payout->update(['processed_at' => now()]);
            
            // Send notification
            $payout->user->notify(new PayoutStatusChanged($payout, $oldStatus, 'denied'));
        }
        
        return $updated;
    }

    /**
     * Create wallet transaction for approved payout
     */
    private function createPayoutTransaction(Payout $payout, User $admin): void
    {
        WalletTransaction::create([
            'user_id' => $payout->user_id,
            'type' => 'payout',
            'amount' => -$payout->amount_requested,
            'meta' => [
                'payout_id' => $payout->id,
                'processed_by' => $admin->name,
                'processed_at' => now(),
                'description' => 'Payout processed by admin'
            ],
        ]);
    }

    /**
     * Get payout statistics for admin dashboard
     */
    public function getPayoutStatistics(): array
    {
        return [
            'total_payouts' => Payout::count(),
            'pending_payouts' => Payout::where('status', 'pending')->count(),
            'approved_payouts' => Payout::where('status', 'approved')->count(),
            'denied_payouts' => Payout::where('status', 'denied')->count(),
            'total_amount_requested' => Payout::sum('amount_requested'),
            'total_amount_approved' => Payout::where('status', 'approved')->sum('amount_requested'),
            'pending_amount' => Payout::where('status', 'pending')->sum('amount_requested'),
        ];
    }

    /**
     * Get all pending payouts
     */
    public function getAllPendingPayouts()
    {
        return Payout::where('status', 'pending')->get();
    }
}