<?php

namespace Illuminate\Routing\Middleware;

class ThrottleRequests
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
