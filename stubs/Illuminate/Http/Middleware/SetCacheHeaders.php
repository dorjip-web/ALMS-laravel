<?php

namespace Illuminate\Http\Middleware;

class SetCacheHeaders
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
