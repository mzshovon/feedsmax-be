<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeWhitelistedIPs
{
    use ApiResponse;

    protected array $allowedEnvironments = ["production"];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->instance("request", $request);
        $environment = config('app.env');
        if(in_array($environment, $this->allowedEnvironments)) {
            $isWhtielisted = $this->sanitizaIpSeriesWise($request->ip());
            if(!$isWhtielisted) {
                return $this->error("{$request->ip()} IP isn't authorized to perform this action", null, Response::HTTP_FORBIDDEN);
            }
        }
        $response = $next($request);
        app()->instance("response", $response);
        return $response;
    }

    /**
     * @param string $clientIP
     *
     * @return bool
     */
    protected function sanitizaIpSeriesWise(string $clientIP): bool
    {
        $match = false;
        $whitelistedIps = config("auth.whitelisted_ips");

        foreach($whitelistedIps as $whitelistedIp) {
            $exploded_whitelisted_ip = explode(".", $whitelistedIp);
            $exploded_client_ip = explode(".", $clientIP);
            $positioned_array = array_slice($exploded_client_ip, 0, count($exploded_whitelisted_ip));
            $match = $whitelistedIp === implode(".", $positioned_array);
            if($match) {
                return $match;
            }
        }
        return $match;
    }
}
