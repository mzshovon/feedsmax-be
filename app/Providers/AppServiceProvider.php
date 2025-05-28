<?php

namespace App\Providers;

use App\Services\Contracts\LoggerServiceInterface;
use App\Services\LoggerService;
use App\Services\LogObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

class AppServiceProvider extends ServiceProvider
{
    protected array $allowedRouteToLog = ["event", "response", "questions"];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app['db']->setDefaultConnection('mysql::write');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $requestScheme = php_sapi_name();
        if($requestScheme == "fpm-fcgi") {
            $sanitzedUrl = strtok($_SERVER['REQUEST_URI'], '?');
            $explodedUrl = explode("/", $sanitzedUrl);
            if(!in_array("cms", $explodedUrl)) {
                $containsAllowedRoute = preg_match("/\/(" . implode("|", $this->allowedRouteToLog) . ")\/(.+)?/", $sanitzedUrl) === 1;
                if($containsAllowedRoute) {
                    $request = app('request');
                    if(!in_array(strtolower($request->method()), ['options', 'head'])) {
                        $this->app->singleton(LoggerServiceInterface::class, LoggerService::class);
                        $loggerService = app(LoggerService::class);
                        $loggerService->init();
                        $this->app->terminating(function () use ($loggerService) {
                            $loggerService->close();
                        });
                    }
                }
            }
        }

        // Register e macro cache delete key-value
        Cache::macro('deleteMatching', function ($pattern) {
            if(config('app.env') == "test") {
                Cache::flush();
            } else {
                $store = Cache::store();
                $cache_prefix = $store->getPrefix(); // Replace with appropriate method for your cache store
                $keys = $store->getRedis()->keys("{$cache_prefix}{$pattern}*"); // Replace with appropriate method for your cache store

                foreach($keys as $key) {
                    Cache::delete(explode($cache_prefix,$key)[1]);
                }
            }
        });
    }
}
