<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
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
        View::share('settings', Setting::first());
        View::share('cartCount', Auth::check() ? Cart::where('user_id', Auth::id())->get(): 0);

    }
}
