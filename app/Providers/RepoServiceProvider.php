<?php

namespace App\Providers;

use App\Repositories\Contracts\HitRepoInterface;
use App\Repositories\Contracts\EventRepoInterface;
use App\Repositories\HitRepo;
use App\Repositories\EventRepo;
use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EventRepoInterface::class, EventRepo::class);
        $this->app->bind(HitRepoInterface::class, HitRepo::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
