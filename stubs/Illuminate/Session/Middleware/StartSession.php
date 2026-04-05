<?php

namespace Illuminate\Session\Middleware;

class StartSession
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
