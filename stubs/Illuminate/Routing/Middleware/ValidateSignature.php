<?php

namespace Illuminate\Routing\Middleware;

class ValidateSignature
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
