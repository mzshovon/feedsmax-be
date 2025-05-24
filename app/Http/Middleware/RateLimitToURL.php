<?php

namespace App\Http\Middleware;

use App\Services\ThemeService;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

class RateLimitToURL extends ThrottleRequests
{
    use ApiResponse;

    protected array $allowedEnvironments = ["production", "test"];

    public function handle($request, Closure $next, $maxAttempts = 1, $decayMinutes = 80, $prefix = '')
    {
        $environment = config('app.env');
        if(in_array($environment, $this->allowedEnvironments)) {
            if (is_string($maxAttempts)
                && func_num_args() === 3
                && ! is_null($limiter = $this->limiter->limiter($maxAttempts))) {
                return $this->handleRequestUsingNamedLimiter($request, $next, $maxAttempts, $limiter);
            }

            return $this->handleRequest(
                $request,
                $next,
                [
                    (object) [
                        'key' => $prefix.$this->resolveRequestSignature($request),
                        'maxAttempts' => $this->resolveMaxAttempts($request, $maxAttempts),
                        'decayMinutes' => $decayMinutes,
                        'responseCallback' => null,
                    ],
                ]
            );
        }

        return $next($request);
    }

    protected function resolveRequestSignature($request)
    {
        return sha1($request->url());
    }

    protected function handleRequest($request, Closure $next, array $limits)
    {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                $parsedURL =  explode("/", $request->url());
                $channel = $parsedURL[count($parsedURL) - 3] ?? "";
                $data["theme"] = ThemeService::theme($channel) ?? [];
                return $this->error('Too Many Request', null, Response::HTTP_TOO_MANY_REQUESTS, $data);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        $response = $next($request);

        foreach ($limits as $limit) {
            $response = $this->addHeaders(
                $response,
                $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts)
            );
        }

        return $response;
    }
}
