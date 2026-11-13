<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\CartService;
use App\Services\LogSmsSender;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsSender::class, LogSmsSender::class);
    }

    public function boot(): void
    {
        View::composer('layouts.shop', function ($view): void {
            $view->with('cartItemCount', app(CartService::class)->itemCount());
        });

        Event::listen(Login::class, function (Login $event): void {
            app(CartService::class)->mergeGuestCartIntoUser($event->user);
        });

        RateLimiter::for('payment-callbacks', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('auth-pages', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(15)->by((string) $key);
        });

        Order::observe(OrderObserver::class);
    }
}
