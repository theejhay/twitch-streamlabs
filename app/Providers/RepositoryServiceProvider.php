<?php

namespace App\Providers;

use App\Repository\Helper\TwitchRepository;
use App\Repository\Eloquent\DashboardRepository;
use App\Repository\Eloquent\UserRepository;
use App\Repository\Interface\DashboardRepositoryInterface;
use App\Repository\Interface\TwitchRepositoryInterface;
use App\Repository\Interface\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TwitchRepositoryInterface::class, TwitchRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
