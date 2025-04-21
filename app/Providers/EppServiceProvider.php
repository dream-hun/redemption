<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Epp\EppService;
use Illuminate\Support\ServiceProvider;

final class EppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EppService::class, function ($app): EppService {
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
