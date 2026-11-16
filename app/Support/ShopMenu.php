<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ShopMenu
{
    public static function headerItems(): Collection
    {
        if (! Schema::hasTable('menus')) {
            return collect();
        }

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
    }

    public static function headerCta(): ?MenuItem
    {
        if (! Schema::hasTable('menus')) {
            return null;
        }

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
    }

    public static function forgetCache(): void
    {
        Cache::forget('shop.menu.header_main');
        Cache::forget('shop.menu.header_cta');
    }
}