<?php

namespace App\Providers;

use App\Repositories\Contracts\TodoRepositoryInterface;
use App\Repositories\Eloquent\TodoRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TodoRepositoryInterface::class,
            TodoRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
