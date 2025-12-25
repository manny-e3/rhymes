<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Services\UserActivityService;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Listen to login event
        Event::listen(function (Login $event) {
            try {
                $userActivityService = app(UserActivityService::class);
                $userActivityService->logLogin($event->user->id);
            } catch (\Exception $e) {
                Log::error('Failed to log login activity: ' . $e->getMessage());
            }
        });

        // Listen to logout event
        Event::listen(function (Logout $event) {
            try {
                $userActivityService = app(UserActivityService::class);
                if ($event->user) {
                    $userActivityService->logLogout($event->user->id);
                }
            } catch (\Exception $e) {
                Log::error('Failed to log logout activity: ' . $e->getMessage());
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}