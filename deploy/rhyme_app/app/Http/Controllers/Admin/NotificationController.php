<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        // Get actual notifications from the database for admin user
        $notifications = Notification::forUser(Auth::id())
            ->latest()
            ->paginate(20);

        // Calculate stats
        $stats = [
            'total' => $notifications->total(),
            'unread' => $notifications->getCollection()->whereNull('read_at')->count(),
            'today' => $notifications->getCollection()->where('created_at', '>=', now()->startOfDay())->count(),
            'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:announcement,system,promotion,maintenance',
            'audience' => 'required|in:all,authors,readers,admins',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'send_method' => 'required|in:in_app,email,both',
        ]);

        try {
            // Get target users based on audience
            $users = $this->getTargetUsers($validated['audience']);

            // Send notifications based on method
            if (in_array($validated['send_method'], ['email', 'both'])) {
                $this->sendEmailNotifications($users, $validated);
            }

            if (in_array($validated['send_method'], ['in_app', 'both'])) {
                $this->sendInAppNotifications($users, $validated);
            }

            return response()->json([
                'success' => true,
                'message' => "Notification sent to {$users->count()} users successfully!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            // Update all notifications for the admin user
            Notification::forUser(Auth::id())
                ->unread()
                ->update(['read_at' => now()]);
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'author_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $author = User::findOrFail($validated['author_id']);

            // Send email to author
            Mail::raw($validated['message'], function ($mail) use ($author, $validated) {
                $mail->to($author->email)
                     ->subject($validated['subject']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTargetUsers($audience)
    {
        switch ($audience) {
            case 'authors':
                return User::role('author')->get();
            case 'readers':
                return User::role('reader')->get();
            case 'admins':
                return User::role('admin')->get();
            case 'all':
            default:
                return User::all();
        }
    }

    private function sendEmailNotifications($users, $data)
    {
        foreach ($users as $user) {
            Mail::raw($data['message'], function ($mail) use ($user, $data) {
                $mail->to($user->email)
                     ->subject($data['title']);
            });
        }
    }

    private function sendInAppNotifications($users, $data)
    {
        // Create in-app notification records for each user
        foreach ($users as $user) {
            Notification::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\SystemAlert',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $user->id,
                'data' => [
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'type' => $data['type'],
                    'priority' => $data['priority'],
                    'icon' => 'ni ni-bell',
                    'action_url' => '#'
                ],
                'title' => $data['title'],
                'message' => $data['message'],
                'icon' => 'ni ni-bell',
                'read_at' => null,
            ]);
        }
    }
}