<?php

namespace Illuminate\Auth\Middleware;

class EnsureEmailIsVerified
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
