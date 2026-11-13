<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Services\CartService;
use App\Services\LogSmsSender;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
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
    }
}
