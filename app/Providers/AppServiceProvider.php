<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Support\AdminBar\AdminBarBuilder;
use App\Support\FrameworkWritablePaths;
use App\Support\PersistentBackupStorage;
use App\Support\PersistentLogStorage;
use App\Support\PublicUploadStorage;
use App\Support\ShopAppearance;
use App\Support\ShopMenu;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\CartService;
use App\Services\LogSmsSender;
use App\Services\Payment\PaymentConfiguration;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsSender::class, LogSmsSender::class);
        $this->app->singleton(PaymentConfiguration::class);
    }

    public function boot(): void
    {
        if (! $this->app->runningUnitTests()) {
            static $storageSymlinkChecked = false;
            if (! $storageSymlinkChecked) {
                $storageSymlinkChecked = true;
                FrameworkWritablePaths::ensurePresent();
                PersistentLogStorage::ensureLogsDirectory();
                PersistentLogStorage::ensureStorageLogsSymlink();
                PersistentBackupStorage::ensureBackupsDirectory();
                PublicUploadStorage::ensurePublicSymlink();
            }
        }

        if ($this->app->environment('production')) {
            $appUrl = (string) config('app.url');

            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        Schema::defaultStringLength(191);

        // Legacy cache key stored serialized ShopSetting models and caused 500 after deploy.
        try {
            Cache::forget('shop_settings.singleton');
        } catch (\Throwable) {
            // Never abort bootstrap when cache (e.g. Redis) is temporarily unavailable.
        }

        View::composer(['layouts.shop', 'components.shop.site-header', 'components.shop.home-category-strip'], function ($view): void {
            $view->with('shopSiteName', ShopAppearance::siteName());
            $view->with('shopWordmark', ShopAppearance::wordmark());
            $view->with('shopLogoUrl', ShopAppearance::logoUrl());
            $view->with('shopAccent', ShopAppearance::accentColor());
            $settings = ShopAppearance::settings();
            $view->with('shopMetaDescription', $settings->meta_description);
            $view->with('shopFooterText', $settings->footer_text);
            $view->with('shopContactEnabled', (bool) ($settings->contact_form_enabled ?? true));
        });

        View::composer('components.shop.site-header', function ($view): void {
            try {
                $view->with('headerMenuItems', ShopMenu::headerItems());
                $view->with('headerCta', ShopMenu::headerCta());
            } catch (\Throwable) {
                $view->with('headerMenuItems', collect());
                $view->with('headerCta', null);
            }
        });

        View::composer('layouts.shop', function ($view): void {
            $cart = app(CartService::class);
            $view->with('cartItemCount', $cart->itemCount());
            $view->with('cartSubtotal', $cart->subtotal());

            $wishlistCount = 0;
            if (auth()->check() && Schema::hasTable('wishlist_items')) {
                $wishlistCount = auth()->user()->wishlistItems()->count();
            }

            $view->with('wishlistCount', $wishlistCount);

            $view->with('adminBar', app(AdminBarBuilder::class)->forShop(request()));
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
