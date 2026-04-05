<?php

namespace Illuminate\Auth\Middleware;

class AuthenticateWithBasicAuth
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
