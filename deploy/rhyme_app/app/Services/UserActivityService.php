<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityService
{
    /**
     * Log a user activity
     *
     * @param string $type
     * @param string $description
     * @param array $metadata
     * @param int|null $userId
     * @return UserActivity
     */
    public function logActivity(string $type, string $description, array $metadata = [], ?int $userId = null): UserActivity
    {
        // Get the current user ID if not provided
        if ($userId === null && Auth::check()) {
            $userId = Auth::id();
        }

        // Create the activity record
        $activity = UserActivity::create([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => $metadata
        ]);

        return $activity;
    }

    /**
     * Get recent activities for a user
     *
     * @param int|null $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserActivities(?int $userId = null, int $limit = 50)
    {
        if ($userId === null && Auth::check()) {
            $userId = Auth::id();
        }

        return UserActivity::forUser($userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by type
     *
     * @param string $type
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivitiesByType(string $type, int $limit = 50)
    {
        return UserActivity::ofType($type)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all recent activities
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities(int $limit = 100)
    {
        return UserActivity::latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Log user login
     *
     * @param int|null $userId
     * @return UserActivity
     */
    public function logLogin(?int $userId = null): UserActivity
    {
        return $this->logActivity('login', 'User logged in', [], $userId);
    }

    /**
     * Log user logout
     *
     * @param int|null $userId
     * @return UserActivity
     */
    public function logLogout(?int $userId = null): UserActivity
    {
        return $this->logActivity('logout', 'User logged out', [], $userId);
    }

    /**
     * Log book submission
     *
     * @param int $bookId
     * @param string $bookTitle
     * @param int|null $userId
     * @return UserActivity
     */
    public function logBookSubmission(int $bookId, string $bookTitle, ?int $userId = null): UserActivity
    {
        return $this->logActivity('book_submission', "Book submitted: {$bookTitle}", [
            'book_id' => $bookId,
            'book_title' => $bookTitle
        ], $userId);
    }

    /**
     * Log book status change
     *
     * @param int $bookId
     * @param string $bookTitle
     * @param string $oldStatus
     * @param string $newStatus
     * @param int|null $userId
     * @return UserActivity
     */
    public function logBookStatusChange(int $bookId, string $bookTitle, string $oldStatus, string $newStatus, ?int $userId = null): UserActivity
    {
        return $this->logActivity('book_status_change', "Book status changed: {$bookTitle} from {$oldStatus} to {$newStatus}", [
            'book_id' => $bookId,
            'book_title' => $bookTitle,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ], $userId);
    }

    /**
     * Log payout request
     *
     * @param int $payoutId
     * @param float $amount
     * @param int|null $userId
     * @return UserActivity
     */
    public function logPayoutRequest(int $payoutId, float $amount, ?int $userId = null): UserActivity
    {
        return $this->logActivity('payout_request', "Payout requested: ₦" . number_format($amount, 2), [
            'payout_id' => $payoutId,
            'amount' => $amount
        ], $userId);
    }

    /**
     * Log payout status change
     *
     * @param int $payoutId
     * @param float $amount
     * @param string $oldStatus
     * @param string $newStatus
     * @param int|null $userId
     * @return UserActivity
     */
    public function logPayoutStatusChange(int $payoutId, float $amount, string $oldStatus, string $newStatus, ?int $userId = null): UserActivity
    {
        return $this->logActivity('payout_status_change', "Payout status changed: ₦" . number_format($amount, 2) . " from {$oldStatus} to {$newStatus}", [
            'payout_id' => $payoutId,
            'amount' => $amount,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ], $userId);
    }

    /**
     * Log user registration
     *
     * @param int $userId
     * @param string $userName
     * @return UserActivity
     */
    public function logUserRegistration(int $userId, string $userName): UserActivity
    {
        return $this->logActivity('registration', "User registered: {$userName}", [
            'registered_user_id' => $userId,
            'registered_user_name' => $userName
        ]);
    }
}