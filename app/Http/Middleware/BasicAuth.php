<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicAuth
{
    /**
     * Handle an incoming request.
     * Validates HTTP Basic Auth credentials against values stored in .env:
     *   FILE_MANAGER_USER  (default: admin)
     *   FILE_MANAGER_PASS  (default: secret)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedUser = env('FILE_MANAGER_USER');
        $expectedPass = env('FILE_MANAGER_PASS');

        $user = $request->getUser();
        $pass = $request->getPassword();

        if ($user !== $expectedUser || $pass !== $expectedPass) {
            return response('Unauthorized.', 401, [
                'WWW-Authenticate' => 'Basic realm="File Manager"',
            ]);
        }

        return $next($request);
    }
}
