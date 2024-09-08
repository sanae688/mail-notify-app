<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\MUserRepository;
use App\Repositories\EloquentMUserRepository;
use App\Repositories\MApiTokenRepository;
use App\Repositories\EloquentMApiTokenRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MUserRepository::class, EloquentMUserRepository::class);
        $this->app->bind(MApiTokenRepository::class, EloquentMApiTokenRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
