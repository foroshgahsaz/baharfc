<?php

namespace App\Support;

use App\Models\ShopSetting;

class ShopMobileNav
{
    public const DEFAULT_BOTTOM_ITEMS = ['home', 'categories', 'search', 'cart', 'account'];

    /**
     * @return list<string>
     */
    public static function bottomNavItemKeys(): array
    {
        $settings = ShopAppearance::settings();
        if (! SchemaGuard::hasColumn('shop_settings', 'mobile_bottom_nav_items')) {
            return self::DEFAULT_BOTTOM_ITEMS;
        }

        $items = $settings->mobile_bottom_nav_items;
        if (! is_array($items) || $items === []) {
            return self::DEFAULT_BOTTOM_ITEMS;
        }

        $allowed = array_flip(self::DEFAULT_BOTTOM_ITEMS);

        return array_values(array_filter(
            $items,
            fn ($key) => is_string($key) && isset($allowed[$key]),
        ));
    }

    public static function bottomNavEnabled(): bool
    {
        if (! SchemaGuard::hasColumn('shop_settings', 'mobile_bottom_nav_enabled')) {
            return true;
        }

        return (bool) ShopAppearance::settings()->mobile_bottom_nav_enabled;
    }

    public static function mobileSearchIconEnabled(): bool
    {
        if (! SchemaGuard::hasColumn('shop_settings', 'mobile_header_search_icon')) {
            return true;
        }

        return (bool) ShopAppearance::settings()->mobile_header_search_icon;
    }

    /**
     * @return list<array{key: string, label: string, href: string, icon: string}>
     */
    public static function resolvedBottomNavItems(): array
    {
        $map = [
            'home' => [
                'label' => 'خانه',
                'href' => route('home'),
                'icon' => 'home',
            ],
            'categories' => [
                'label' => 'دسته‌ها',
                'href' => route('products.index'),
                'icon' => 'grid',
            ],
            'search' => [
                'label' => 'جستجو',
                'href' => '#shop-search',
                'icon' => 'search',
            ],
            'cart' => [
                'label' => 'سبد',
                'href' => route('cart.index'),
                'icon' => 'cart',
            ],
            'account' => [
                'label' => 'حساب',
                'href' => auth()->check() ? route('account.index') : route('login'),
                'icon' => 'user',
            ],
        ];

        $items = [];
        foreach (self::bottomNavItemKeys() as $key) {
            if (! isset($map[$key])) {
                continue;
            }
            $items[] = array_merge(['key' => $key], $map[$key]);
        }

        return $items;
    }
}