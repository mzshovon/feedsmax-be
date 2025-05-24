<?php

namespace App\Http\Controllers;

use App\Services\HealthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppHealthController extends Controller
{
    public function startUp(HealthService $healthService)
    {
        try {
            [$statusCode, $message] = $healthService->getStartUpReport();
            return response(
                [
                    "statusCode" => $statusCode,
                    "message" => $message
                ],
                $statusCode
            );

        } catch (\Exception $ex) {
            $startErrorStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response(
                [
                    "statusCode" => $startErrorStatus,
                    "message" => $ex->getMessage()
                ],
                $startErrorStatus
            );
        }
    }

    public function ready(HealthService $healthService)
    {
        try {
            [$statusCode, $message] = $healthService->getReadynessReport();
            return response(
                [
                    "statusCode" => $statusCode,
                    "message" => $message
                ],
                $statusCode
            );

        } catch (\Exception $ex) {
            $startErrorStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response(
                [
                    "statusCode" => $startErrorStatus,
                    "message" => $ex->getMessage()
                ],
                $startErrorStatus
            );
        }
    }

    public function live()
    {
        try {
            $startUpStatus = Response::HTTP_OK;
            return response(
                [
                    "statusCode" => $startUpStatus,
                    "message" => "The application is live!"
                ],
                $startUpStatus
            );

        } catch (\Exception $ex) {
            $startErrorStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response(
                [
                    "statusCode" => $startErrorStatus,
                    "message" => $ex->getMessage()
                ],
                $startErrorStatus
            );
        }
    }

    public function redisFailOver(HealthService $healthService)
    {
        try {
            [$statusCode, $message] = $healthService->testRedisFailOver();
            return response(
                [
                    "statusCode" => $statusCode,
                    "message" => $message
                ],
                $statusCode
            );

        } catch (\Exception $ex) {
            $startErrorStatus = Response::HTTP_INTERNAL_SERVER_ERROR;
            return response(
                [
                    "statusCode" => $startErrorStatus,
                    "message" => $ex->getMessage()
                ],
                $startErrorStatus
            );
        }
    }
}
