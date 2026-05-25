<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class LogPageVisits
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldLog($request)) {
            $this->createLogEntry($request);
        }

        return $next($request);
    }

    protected function shouldLog(Request $request): bool
    {
        // Only log GET requests (exclude POST, PUT, DELETE, etc.)
        if (!$request->isMethod('GET')) {
            return false;
        }

        // Skip logging for certain paths (optional - customize as needed)
        if ($this->shouldSkipPath($request)) {
            return false;
        }

        // Get or create client ID (like GA4's _ga cookie)
        $clientId = $this->getOrCreateClientId($request);
        
        // Check if we need to start a new session
        $shouldStartNewSession = $this->shouldStartNewSession($request, $clientId);
        
        return $shouldStartNewSession;
    }

    protected function getOrCreateClientId(Request $request): string
    {
        // Check if client ID exists in cookie (like GA4's _ga cookie)
        $clientId = $request->cookie('_ga_client_id');
        
        if (!$clientId) {
            // Generate new client ID (format: GA1.2.{random_id}.{timestamp})
            $randomId = mt_rand(100000000, 999999999);
            $timestamp = time();
            $clientId = "GA1.2.{$randomId}.{$timestamp}";
            
            // Set cookie for 2 years (like GA4)
            cookie()->queue('_ga_client_id', $clientId, 60 * 24 * 365 * 2);
        }
        
        return $clientId;
    }

    protected function shouldStartNewSession(Request $request, string $clientId): bool
    {
        // Get session data for this client
        $sessionKey = "ga_session_{$clientId}";
        $sessionData = $request->session()->get($sessionKey);
        
        if (!$sessionData) {
            // No session data exists, start new session
            return true;
        }
        
        // Check if session has expired (30 minutes of inactivity)
        try {
            $lastActivity = Carbon::parse($sessionData['last_activity']);
            $sessionTimeout = now()->subMinutes(30);
            
            if ($lastActivity->lt($sessionTimeout)) {
                // Session expired, start new session
                $request->session()->forget($sessionKey);
                return true;
            }
            
            // Session is still active, update last activity but don't log
            $this->updateSessionActivity($request, $clientId);
            return false;
            
        } catch (\Exception $e) {
            // Invalid session data, clear and start new session
            $request->session()->forget($sessionKey);
            \Log::warning('Invalid GA session data, clearing: ' . $e->getMessage());
            return true;
        }
    }

    protected function updateSessionActivity(Request $request, string $clientId): void
    {
        $sessionKey = "ga_session_{$clientId}";
        $sessionData = $request->session()->get($sessionKey, []);
        
        $sessionData['last_activity'] = now();
        $sessionData['page_views'] = ($sessionData['page_views'] ?? 0) + 1;
        
        $request->session()->put($sessionKey, $sessionData);
    }

    protected function shouldSkipPath(Request $request): bool
    {
        // Skip logging for these paths (customize as needed)
        $skipPaths = [
            '/admin/*',           // Admin pages
            '/api/*',             // API routes
            '/_debugbar/*',       // Debug bar
            '/storage/*',         // Storage files
            '/favicon.ico',       // Favicon
            '*.css',              // CSS files
            '*.js',               // JavaScript files
            '*.png',              // Image files
            '*.jpg',
            '*.jpeg',
            '*.gif',
            '*.svg',
        ];

        $currentPath = $request->path();
        
        foreach ($skipPaths as $skipPath) {
            if (str_contains($skipPath, '*')) {
                // Handle wildcard patterns
                $pattern = str_replace('*', '.*', $skipPath);
                if (preg_match('#^' . $pattern . '$#', $currentPath)) {
                    return true;
                }
            } else {
                // Exact match
                if ($currentPath === ltrim($skipPath, '/')) {
                    return true;
                }
            }
        }

        return false;
    }

 protected function createLogEntry(Request $request)
{
    try {
        // Get client ID
        $clientId = $this->getOrCreateClientId($request);

        // Check if this is a new user
        $isNewUser = $this->isNewUser($clientId);

        // Create session data
        $sessionKey = "ga_session_{$clientId}";
        $sessionData = [
            'session_id'    => Str::uuid(),
            'start_time'    => now(),
            'last_activity' => now(),
            'page_views'    => 1,
            'is_new_user'   => $isNewUser
        ];

        $request->session()->put($sessionKey, $sessionData);

        // Get IP (for testing, replace with real public IP)
        $ipAddress = $this->getClientIp($request);

        // Initialize location
        $location = null;

        // Skip private IPs
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $url = "http://ip-api.com/php/" . $ipAddress;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response !== false) {
                $data = @unserialize($response);
                if (is_array($data) && $data['status'] === 'success') {
                    $location = $data['city'] . ", " . $data['regionName'] . ", " . $data['country'];
                }
            }
        }

        // Save to DB
        VisitorLog::create([
            'ip_address'   => $ipAddress,
            'location'     => $location,
            'user_agent'   => $request->userAgent() ?? 'Unknown',
            'referer'      => $request->header('referer', 'Direct'),
            'path'         => $request->path(),
            'visited_at'   => now(),
            'user_id'      => optional(auth()->user())->id,
            'client_id'    => $clientId,
            'session_id'   => $sessionData['session_id'],
            'is_new_user'  => $isNewUser,
            'request_data' => $this->getSanitizedRequestData($request)
        ]);

    } catch (\Exception $e) {
        \Log::error('Visitor logging failed: '.$e->getMessage());
    }
}


    protected function isNewUser(string $clientId): bool
    {
        // Check if this client ID has been seen before
        return !VisitorLog::where('client_id', $clientId)->exists();
    }

    protected function getClientIp(Request $request): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if ($ip = $request->server($key)) {
                return $ip;
            }
        }
        return $request->ip() ?? '0.0.0.0';
    }

    protected function getSanitizedRequestData(Request $request): array
    {
        return [
            'headers' => $request->headers->all(),
            'path' => $request->path(),
            'query' => $request->query(),
            'method' => $request->method(),
            'client_id' => $request->cookie('_ga_client_id'),
            // Exclude sensitive data
        ];
    }
}