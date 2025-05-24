<?php

namespace App\Providers;

use App\Repositories\Contracts\HitRepoInterface;
use App\Repositories\Contracts\TriggerRepoInterface;
use App\Repositories\HitRepo;
use App\Repositories\TriggerRepo;
use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TriggerRepoInterface::class, TriggerRepo::class);
        $this->app->bind(HitRepoInterface::class, HitRepo::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
