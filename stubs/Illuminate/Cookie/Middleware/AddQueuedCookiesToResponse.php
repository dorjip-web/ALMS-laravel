<?php

namespace Illuminate\Cookie\Middleware;

class AddQueuedCookiesToResponse
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
