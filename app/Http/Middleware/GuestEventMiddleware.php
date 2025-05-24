<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestEventMiddleware
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $parsed_index = 2;
        $requestURL = $request->url();
        $parsedURL =  explode("/", $requestURL);
        if(in_array("trigger", $parsedURL)) {
            $parsed_index = 1;
        }
        $event = $parsedURL[count($parsedURL) - $parsed_index];
        //! Should be check string contains guest substring
        if(str_contains($event,"guest")) {
            $request['event'] = $event;
            $request['msisdn'] = generateRandomString();
            $request['device_id'] = "GUEST";
        } else {
            if(!$request['msisdn']) {
                return $this->error("msisdn field is required!", null, Response::HTTP_BAD_REQUEST);
            }
            $msisdn_validation_pattern = "/^01[3-9][0-9]{8}$/u";
            if(!preg_match($msisdn_validation_pattern, $request['msisdn'])) {
                return $this->error("MSISDN format is invalid!", null, Response::HTTP_BAD_REQUEST);
            }
        }
        return $next($request);
    }
}
