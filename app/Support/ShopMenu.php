<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
class ShopMenu
{
    public static function headerItems(): Collection
    {
        if (! SchemaGuard::hasTable('menus')) {
            return collect();
        }

        try {
            return Cache::remember('shop.menu.header_main', 300, function () {
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
            });
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
            return Cache::remember('shop.menu.header_cta', 300, function () {
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
            });
        } catch (\Throwable) {
            return null;
        }
    }

    public static function forgetCache(): void
    {
        try {
            Cache::forget('shop.menu.header_main');
            Cache::forget('shop.menu.header_cta');
        } catch (\Throwable) {
            // Redis موقتاً قطع — نادیده بگیر
        }
    }
}