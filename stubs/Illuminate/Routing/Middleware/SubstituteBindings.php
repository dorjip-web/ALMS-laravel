<?php

namespace Illuminate\Routing\Middleware;

class SubstituteBindings
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
