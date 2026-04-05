<?php

namespace Illuminate\Session\Middleware;

class AuthenticateSession
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
