<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ResilientThrottleRequestsMiddleware
{
    public function handle($request, Closure $next, $decayMinutes = 1)
    {
        return $next($request);
    }
}
