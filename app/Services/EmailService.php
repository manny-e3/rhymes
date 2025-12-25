<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class EmailService
{
    /**
     * Send a personal email to a specific user
     */
    public function sendPersonalEmail(User $user, string $subject, string $message, string $template = null): bool
    {
        try {
            // Ensure message is a proper string
            $message = is_string($message) ? $message : (string) $message;
            
            // Log the type of message for debugging
            Log::debug('Sending personal email', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'subject' => $subject,
                'message_type' => gettype($message),
                'message_class' => is_object($message) ? get_class($message) : null,
            ]);
            
            $user->notify(new EmailNotification($subject, $message, $template));
            
            Log::info('Personal email sent successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'subject' => $subject,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send personal email', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'subject' => $subject,
                'message_type' => gettype($message),
                'message_class' => is_object($message) ? get_class($message) : null,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send bulk emails to multiple users
     */
    public function sendBulkEmails(array $userIds, string $subject, string $message, string $template = null): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'total' => count($userIds),
        ];

        // Get users by IDs
        $users = User::whereIn('id', $userIds)->get();

        // Ensure message is a proper string
        $message = is_string($message) ? $message : (string) $message;
        
        foreach ($users as $user) {
            try {
                // Log the type of message for debugging
                Log::debug('Sending bulk email', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'subject' => $subject,
                    'message_type' => gettype($message),
                    'message_class' => is_object($message) ? get_class($message) : null,
                ]);
                
                $user->notify(new EmailNotification($subject, $message, $template));
                
                $results['sent']++;
                
                Log::info('Bulk email sent successfully', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'subject' => $subject,
                ]);
            } catch (\Exception $e) {
                $results['failed']++;
                
                Log::error('Failed to send bulk email', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'subject' => $subject,
                    'message_type' => gettype($message),
                    'message_class' => is_object($message) ? get_class($message) : null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Send bulk emails to all authors
     */
    public function sendBulkEmailsToAuthors(string $subject, string $message, string $template = null): array
    {
        // Get all users who have the 'author' role
        $authors = User::whereHas('roles', function ($query) {
            $query->where('name', 'author');
        })->get();

        $userIds = $authors->pluck('id')->toArray();

        return $this->sendBulkEmails($userIds, $subject, $message, $template);
    }

    /**
     * Send bulk emails to all users (with role filter option)
     */
    public function sendBulkEmailsToUsers(string $subject, string $message, string $template = null, array $roleFilters = []): array
    {
        $query = User::query();

        if (!empty($roleFilters)) {
            $query->whereHas('roles', function ($q) use ($roleFilters) {
                $q->whereIn('name', $roleFilters);
            });
        }

        $users = $query->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendBulkEmails($userIds, $subject, $message, $template);
    }

    /**
     * Send bulk emails to users based on custom criteria
     */
    public function sendBulkEmailsToFilteredUsers(string $subject, string $message, callable $filterCallback, string $template = null): array
    {
        $users = User::where($filterCallback)->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendBulkEmails($userIds, $subject, $message, $template);
    }

    /**
     * Send test email to a specific user
     */
    public function sendTestEmail(User $user, string $subject, string $message, string $template = null): bool
    {
        // Ensure message is a proper string
        $message = is_string($message) ? $message : (string) $message;
        
        return $this->sendPersonalEmail($user, "[TEST] $subject", $message, $template);
    }

    /**
     * Get email statistics
     */
    public function getEmailStats(): array
    {
        $totalUsers = User::count();
        $totalAuthors = User::whereHas('roles', function ($query) {
            $query->where('name', 'author');
        })->count();

        return [
            'total_users' => $totalUsers,
            'total_authors' => $totalAuthors,
            'non_authors' => $totalUsers - $totalAuthors,
        ];
    }
}