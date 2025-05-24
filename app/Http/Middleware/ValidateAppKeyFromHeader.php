<?php

namespace App\Http\Middleware;

use App\Classes\EncryptDecryptManager;
use App\Models\Channel;
use App\Repositories\ChannelRepo;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAppKeyFromHeader
{
    use ApiResponse;

    const CHANNEL_URI_POS = 4;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $environment = config('app.env');
            if($environment !== "local") {
                $parsedURL =  explode("/",$request->getRequestUri());
                $channelName =  $parsedURL[self::CHANNEL_URI_POS];
                $token = $request->header("X-App-Token") ?? null;
                $repo = new ChannelRepo(new Channel());
                $channelInfo= $repo->getInfoByChannelTag($channelName);
                $verifyToken = EncryptDecryptManager::verifyToken($token, $channelInfo);
                if(!$verifyToken) {
                    return $this->error("Invalid APP_KEY_TOKEN provided!", null, Response::HTTP_UNAUTHORIZED);
                }
            }

            return $next($request);
        }catch (\Exception $ex){
            return $this->error("No matching channel found", null, Response::HTTP_UNAUTHORIZED);
        }

    }
}
