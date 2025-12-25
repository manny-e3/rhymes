<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\UserActivityService;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    protected $userActivityService;

    public function __construct(UserActivityService $userActivityService)
    {
        $this->userActivityService = $userActivityService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track activities for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            
            // Track specific actions based on the request method and route
            $method = $request->method();
            $routeName = $request->route() ? $request->route()->getName() : null;
            
            try {
                // Track form submissions and updates
                if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
                    $this->trackActionBasedOnRoute($routeName, $request, $user->id);
                }
                
                // Track specific GET requests that are significant
                if ($method === 'GET' && $this->shouldTrackGetRequest($routeName)) {
                    $this->trackGetRequest($routeName, $request, $user->id);
                }
            } catch (\Exception $e) {
                // Log the error but don't break the request
                Log::error('Failed to log user activity: ' . $e->getMessage());
            }
        }

        return $response;
    }
    
    /**
     * Track actions based on route name for POST, PUT, PATCH requests
     */
    private function trackActionBasedOnRoute($routeName, $request, $userId)
    {
        switch ($routeName) {
            case 'profile.update':
                $this->userActivityService->logActivity(
                    'profile_update',
                    "User updated profile information",
                    [
                        'route' => $routeName,
                        'fields_updated' => array_keys($request->except(['_token', '_method'])),
                    ],
                    $userId
                );
                break;
                
            case 'profile.password':
                $this->userActivityService->logActivity(
                    'password_change',
                    "User changed password",
                    ['route' => $routeName],
                    $userId
                );
                break;
                
            case 'user.books.store':
                $this->userActivityService->logActivity(
                    'book_submission',
                    "User submitted a new book",
                    [
                        'route' => $routeName,
                        'book_title' => $request->input('title'),
                    ],
                    $userId
                );
                break;
                
            case 'admin.users.update':
                $this->userActivityService->logActivity(
                    'user_update',
                    "Admin updated user information",
                    [
                        'route' => $routeName,
                        'target_user_id' => $request->route('user')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            case 'admin.users.activate':
                $this->userActivityService->logActivity(
                    'user_activation',
                    "Admin activated user account",
                    [
                        'route' => $routeName,
                        'target_user_id' => $request->route('user')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            case 'admin.users.deactivate':
                $this->userActivityService->logActivity(
                    'user_deactivation',
                    "Admin deactivated user account",
                    [
                        'route' => $routeName,
                        'target_user_id' => $request->route('user')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            case 'admin.payouts.approve':
                $this->userActivityService->logActivity(
                    'payout_approval',
                    "Admin approved payout",
                    [
                        'route' => $routeName,
                        'payout_id' => $request->route('payout')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            case 'admin.payouts.deny':
                $this->userActivityService->logActivity(
                    'payout_denial',
                    "Admin denied payout",
                    [
                        'route' => $routeName,
                        'payout_id' => $request->route('payout')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            default:
                // For other POST/PUT/PATCH requests, log a generic activity
                $this->userActivityService->logActivity(
                    'form_submission',
                    "User submitted form: {$routeName}",
                    [
                        'route' => $routeName,
                        'method' => $request->method(),
                    ],
                    $userId
                );
                break;
        }
    }
    
    /**
     * Check if a GET request should be tracked
     */
    private function shouldTrackGetRequest($routeName)
    {
        $trackedRoutes = [
            'admin.dashboard',
            'admin.users.index',
            'admin.users.show', 
            'admin.books.index',
            'admin.books.show',
            'admin.payouts.index',
            'admin.payouts.show',
        ];
        
        return in_array($routeName, $trackedRoutes);
    }
    
    /**
     * Track significant GET requests
     */
    private function trackGetRequest($routeName, $request, $userId)
    {
        switch ($routeName) {
            case 'admin.users.show':
                $this->userActivityService->logActivity(
                    'user_profile_view',
                    "Admin viewed user profile",
                    [
                        'route' => $routeName,
                        'target_user_id' => $request->route('user')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            case 'admin.books.show':
                $this->userActivityService->logActivity(
                    'book_details_view',
                    "Admin viewed book details",
                    [
                        'route' => $routeName,
                        'book_id' => $request->route('book')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            case 'admin.payouts.show':
                $this->userActivityService->logActivity(
                    'payout_details_view',
                    "Admin viewed payout details",
                    [
                        'route' => $routeName,
                        'payout_id' => $request->route('payout')->id ?? null,
                    ],
                    $userId
                );
                break;
                
            default:
                $this->userActivityService->logActivity(
                    'page_view',
                    "User viewed page: {$routeName}",
                    [
                        'route' => $routeName,
                        'url' => $request->fullUrl(),
                    ],
                    $userId
                );
                break;
        }
    }
}