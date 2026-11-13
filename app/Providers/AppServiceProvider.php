<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Services\LogSmsSender;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsSender::class, LogSmsSender::class);
    }

    public function boot(): void
    {
        //
    }
}
