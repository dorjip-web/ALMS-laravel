<?php

namespace Fruitcake\Cors;

class HandleCors
{
    public function handle($request, $next)
    {
        return $next($request);
    }
}
