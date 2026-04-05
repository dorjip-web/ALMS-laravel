<?php

namespace Illuminate\Foundation\Http\Middleware;

class ValidatePostSize
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
