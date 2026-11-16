<?php

namespace App\Support;

use App\Models\ShopSetting;
use Illuminate\Support\Facades\Schema;

class ShopAppearance
{
    public static function settings(): ShopSetting
    {
        return ShopSetting::current();
    }

    public static function siteName(): string
    {
        return self::settings()->displaySiteName();
    }

    public static function wordmark(): string
    {
        return self::settings()->displayWordmark();
    }

    public static function logoUrl(): ?string
    {
        return self::settings()->logoUrl();
    }

    public static function accentColor(): string
    {
        return self::settings()->displayAccent();
    }

    public static function maintenanceActive(): bool
    {
        if (! Schema::hasTable('shop_settings')) {
            return false;
        }

        return (bool) self::settings()->maintenance_enabled;
    }

    public static function maintenanceMessage(): string
    {
        $message = self::settings()->maintenance_message;

        return filled($message)
            ? $message
            : 'فروشگاه موقتاً در دست تعمیر است. به‌زودی باز می‌گردیم.';
    }
}