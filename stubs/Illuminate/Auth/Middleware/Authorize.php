<?php

namespace Illuminate\Auth\Middleware;

class Authorize
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
