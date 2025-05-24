<?php

namespace App\Http\Middleware;

use App\Classes\CryptoTokenManager;
use App\Classes\EncryptDecryptManager;
use App\Services\CMS\ThemeService;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UUIDTokenValidate
{
    use ApiResponse;

    protected array $envIndices = [
        "production" => "p",
        "test" => "t",
        "staging" => "t",
        "local" => "l",
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uuid = "";
        $requestURL = $request->url();
        $parsedURL =  explode("/", $requestURL);
        $uuid = $parsedURL[count($parsedURL) - 1];
        [$attemptId, $channel, $env, $extra] = (new CryptoTokenManager())->decrypt($uuid);
        if (!$attemptId || !$channel || !$extra || !$env || ($env !== $this->envIndices[config('app.env')])) {
            $channel = $parsedURL[count($parsedURL) - 3] ?? "";
            $data["theme"] = ThemeService::theme($channel) ?? [];
            return $this->error('Token is invalid', null, Response::HTTP_FORBIDDEN, $data);
        }

        $request['attemptId'] = $attemptId;
        $request['channel'] = $channel;
        $request['msisdn'] = $extra;

        return $next($request);
    }
}
