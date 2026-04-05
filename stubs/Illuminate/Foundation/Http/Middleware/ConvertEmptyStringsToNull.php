<?php

namespace Illuminate\Foundation\Http\Middleware;

class ConvertEmptyStringsToNull
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
