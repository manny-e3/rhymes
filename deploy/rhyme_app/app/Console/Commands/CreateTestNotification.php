<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Str;

class CreateTestNotification extends Command
{
    protected $signature = 'notification:test {user_id?}';
    protected $description = 'Create a test notification for a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // Get first admin user
            $user = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->first();
            
            if (!$user) {
                // Get any user
                $user = User::first();
            }
            
            if (!$user) {
                $this->error('No users found in database!');
                return 1;
            }
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }
        }
        
        $this->info("Creating test notification for user: {$user->name} (ID: {$user->id})");
        
        // Create notification
        $notification = Notification::create([
            'id' => Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'test',
                'title' => 'Test Notification',
                'message' => 'This is a test notification created at ' . now()->format('Y-m-d H:i:s'),
                'icon' => 'ni ni-bell',
                'action_url' => '#',
            ],
            'read_at' => null,
        ]);
        
        $this->info('✓ Test notification created successfully!');
        $this->info("Notification ID: {$notification->id}");
        $this->line('');
        $this->info('Now refresh your admin panel and check the notification bell icon.');
        
        // Show current notification count
        $totalNotifications = Notification::where('notifiable_id', $user->id)->count();
        $unreadNotifications = Notification::where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
        
        $this->line('');
        $this->info("User now has:");
        $this->line("  - Total notifications: {$totalNotifications}");
        $this->line("  - Unread notifications: {$unreadNotifications}");
        
        return 0;
    }
}
