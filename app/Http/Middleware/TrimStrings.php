<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrimStrings
{
    public function handle(Request $request, Closure $next)
    {
        // naive pass-through; real implementation trims input strings
        return $next($request);
    }
}
