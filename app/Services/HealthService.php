<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Opcodes\LogViewer\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class HealthService {

    /**
     * @return array
     */
    public function getStartUpReport() : array
    {
        try {
            $startUpStatus = Response::HTTP_OK;
            $message = "Startup health is ok!";
            return [$startUpStatus, $message];

        } catch (\Exception $ex) {
            return [Response::HTTP_INTERNAL_SERVER_ERROR, $ex->getMessage()];
        }
    }

    public function getReadynessReport()
    {
        $status = [
            'database' => $this->checkDatabaseConnection(),
            'redis' => $this->checkRedisConnection(),
        ];
        $isHealthy = $status['database'] && $status['redis'];

        if($isHealthy) {
            return [Response::HTTP_OK, "The application is ready to operate!"];
        } else {
            $errorMessage = !$status['database'] ? "Database is unreachable or down!" : "Redis server is unreachable or down!";
            return [Response::HTTP_INTERNAL_SERVER_ERROR, $errorMessage];
        }
    }

    public function getLivenessReport(){
        // Might get response from standalone operator
    }

    /**
     * @return bool
     */
    private function checkDatabaseConnection() : bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function checkRedisConnection() : bool
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function testRedisFailOver()
    {
//        if ($this->checkRedisConnection()) {
//            Log::info("Redis is ready to operate!");
//        } else {
//            Log::error("Redis is not connected!");
//        }
//        dd($this->checkRedisConnection());
        try {

//            dd(dd(config('database.redis.sentinel.sentinels')));
            $output = Cache::put('sentinel2', 'Connect');
//            Redis::set('test', 'working');
            $result = Cache::get('sentinel2');
            Log::info($result);
            return [Response::HTTP_OK, $result];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), $e->getTrace()];
        }
    }
}
