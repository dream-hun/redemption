<?php

namespace App\Providers;

use App\Services\Epp\EppService;
use Illuminate\Support\ServiceProvider;

class EppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EppService::class, function ($app) {
            return new EppService;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
