<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class ShopMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('menus')) {
            return;
        }

        $menu = Menu::query()->firstOrCreate(
            ['slug' => 'header_main'],
            [
                'name' => 'منوی اصلی هدر',
                'description' => 'نوار ناوبری بالای فروشگاه',
                'is_active' => true,
            ]
        );

        if ($menu->items()->exists()) {
            return;
        }

        $sort = 0;
        foreach ([
            ['label' => 'زنانه', 'item_type' => 'category', 'category_slug' => 'women'],
            ['label' => 'مردانه', 'item_type' => 'category', 'category_slug' => 'men'],
        ] as $row) {
            $categoryId = \App\Models\Category::query()->where('slug', $row['category_slug'])->value('id');
            if ($categoryId) {
                MenuItem::query()->create([
                    'menu_id' => $menu->id,
                    'label' => $row['label'],
                    'item_type' => 'category',
                    'category_id' => $categoryId,
                    'sort_order' => $sort++,
                    'is_active' => true,
                ]);
            }
        }

        foreach (config('shop.nav_links', []) as $link) {
            $pageKey = null;
            $query = null;
            if (($link['route'] ?? '') === 'home') {
                $pageKey = 'home';
            } elseif (($link['route'] ?? '') === 'products.index' && isset($link['params']['q'])) {
                $pageKey = 'products';
                $query = $link['params'];
            } elseif (($link['route'] ?? '') === 'products.index') {
                $pageKey = 'products';
            }

            MenuItem::query()->create([
                'menu_id' => $menu->id,
                'label' => $link['label'],
                'item_type' => $pageKey ? 'page' : 'custom_url',
                'page_key' => $pageKey,
                'query_params' => $query,
                'custom_url' => $pageKey ? null : route($link['route'], $link['params'] ?? []),
                'show_divider_before' => ($link['label'] ?? '') === 'صفحه اصلی',
                'sort_order' => $sort++,
                'is_active' => true,
            ]);
        }

        Menu::query()->firstOrCreate(
            ['slug' => 'header_cta'],
            [
                'name' => 'دکمه پیشنهاد ویژه (هدر)',
                'description' => 'لینک بنفش سمت چپ نوار',
                'is_active' => true,
            ],
        );

        $cta = Menu::query()->where('slug', 'header_cta')->first();
        if ($cta && ! $cta->items()->exists()) {
            MenuItem::query()->create([
                'menu_id' => $cta->id,
                'label' => 'پیشنهاد ویژه',
                'item_type' => 'page',
                'page_key' => 'products_special',
                'sort_order' => 0,
                'is_active' => true,
            ]);
        }
    }
}