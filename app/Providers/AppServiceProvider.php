<?php

namespace App\Providers;

use App\Models\DomainPricing;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cache TLDs for 1 hour
        View::share('tlds', Cache::remember('active_tlds', 3600, function () {
            return DomainPricing::where('status', 'active')
                ->select('tld', 'register_price')
                ->get();
        }));

        View::share('settings', Setting::first());

    }
}
