<?php

namespace Illuminate\Auth\Middleware;

class RequirePassword
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
