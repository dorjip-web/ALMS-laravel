<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ScopedSessionCookie
{
    /**
     * Handle an incoming request.
     * If the request targets admin routes (prefix /admin or route name starting with admin.),
     * switch the session cookie name so admin uses a separate session.
     *
     * IMPORTANT: This middleware MUST run before the StartSession middleware.
     * Register it in `app/Http/Kernel.php` before `\Illuminate\Session\Middleware\StartSession::class`.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $routeName = (string) optional($request->route())->getName();

        // Map route prefix / name prefix to cookie names. Add more mappings as needed.
        $mappings = [
            'admin' => env('ADMIN_SESSION_COOKIE', 'admin_session'),
            'ms' => env('MS_SESSION_COOKIE', 'ms_session'),
            'hod' => env('HOD_SESSION_COOKIE', 'hod_session'),
        ];

        $selectedCookie = null;

        foreach ($mappings as $prefix => $cookieName) {
            if (str_starts_with($path, $prefix) || str_starts_with($routeName, $prefix . '.')) {
                $selectedCookie = $cookieName;
                break;
            }
        }

        if ($selectedCookie) {
            config(['session.cookie' => $selectedCookie]);
        } else {
            // default session cookie (falls back to config/session.php value)
            config(['session.cookie' => env('SESSION_COOKIE', config('session.cookie'))]);
        }

        return $next($request);
    }
}
