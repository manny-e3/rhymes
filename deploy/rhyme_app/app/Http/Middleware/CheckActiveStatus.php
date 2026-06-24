<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->isActive()) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been deactivated. Please contact support.'], 403);
            }
            
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact support.'
            ]);
        }

        return $next($request);
    }
}