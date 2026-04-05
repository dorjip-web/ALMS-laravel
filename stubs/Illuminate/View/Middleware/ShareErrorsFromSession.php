<?php

namespace Illuminate\View\Middleware;

class ShareErrorsFromSession
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
