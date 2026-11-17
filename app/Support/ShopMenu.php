<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ShopMenu
{
    public const HEADER_CACHE_KEY = 'shop.menu.header_main';

    public const CTA_CACHE_KEY = 'shop.menu.header_cta';

    public const PAGE_CACHE_VERSION_KEY = 'shop.page_cache_version';

    public static function headerItems(): Collection
    {
        if (! SchemaGuard::hasTable('menus')) {
            return collect();
        }

        try {
            $cached = Cache::get(self::HEADER_CACHE_KEY);
            if ($cached instanceof Collection && $cached->isNotEmpty()) {
                return $cached;
            }

            $items = self::loadHeaderItems();
            if ($items->isNotEmpty()) {
                Cache::put(self::HEADER_CACHE_KEY, $items, 300);
            }

            return $items;
        } catch (\Throwable) {
            return collect();
        }
    }

    public static function headerCta(): ?MenuItem
    {
        if (! SchemaGuard::hasTable('menus')) {
            return null;
        }

        try {
            $cached = Cache::get(self::CTA_CACHE_KEY);
            if ($cached instanceof MenuItem) {
                return $cached;
            }

            $cta = self::loadHeaderCta();
            if ($cta) {
                Cache::put(self::CTA_CACHE_KEY, $cta, 300);
            }

            return $cta;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function pageCacheVersion(): int
    {
        try {
            return max(1, (int) Cache::get(self::PAGE_CACHE_VERSION_KEY, 1));
        } catch (\Throwable) {
            return 1;
        }
    }

    public static function forgetCache(): void
    {
        try {
            Cache::forget(self::HEADER_CACHE_KEY);
            Cache::forget(self::CTA_CACHE_KEY);
            Cache::add(self::PAGE_CACHE_VERSION_KEY, 1);
            Cache::increment(self::PAGE_CACHE_VERSION_KEY);
        } catch (\Throwable) {
            // Redis موقتاً قطع — نادیده بگیر
        }
    }

    private static function loadHeaderItems(): Collection
    {
        $menu = Menu::query()
            ->where('slug', 'header_main')
            ->where('is_active', true)
            ->first();

        if (! $menu) {
            return collect();
        }

        return $menu->rootItems()
            ->where('is_active', true)
            ->with(['category', 'product', 'blogPost'])
            ->get();
    }

    private static function loadHeaderCta(): ?MenuItem
    {
        $menu = Menu::query()
            ->where('slug', 'header_cta')
            ->where('is_active', true)
            ->first();

        if (! $menu) {
            return null;
        }

        return $menu->rootItems()
            ->where('is_active', true)
            ->with(['category', 'product', 'blogPost'])
            ->first();
    }
}
