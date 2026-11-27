<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FashionShopMenuSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('menus')) {
            return;
        }

        $menu = Menu::query()->updateOrCreate(
            ['slug' => 'header_main'],
            [
                'name' => 'منوی اصلی هدر',
                'description' => 'منوی فروشگاه پوشاک (الهام از هیبا و آی‌بولک)',
                'is_active' => true,
            ],
        );

        $menu->items()->delete();

        $sort = 0;
        foreach ($this->menuRows() as $row) {
            $categoryId = null;
            if (($row['category_slug'] ?? null) !== null) {
                $categoryId = Category::query()->where('slug', $row['category_slug'])->value('id');
            }

            if ($row['item_type'] === 'category' && $categoryId === null) {
                continue;
            }

            MenuItem::query()->create([
                'menu_id' => $menu->id,
                'label' => $row['label'],
                'item_type' => $row['item_type'],
                'category_id' => $categoryId,
                'page_key' => $row['page_key'] ?? null,
                'custom_url' => $row['custom_url'] ?? null,
                'sort_order' => $sort++,
                'is_active' => true,
            ]);
        }

        Menu::query()->updateOrCreate(
            ['slug' => 'header_cta'],
            [
                'name' => 'دکمه پیشنهاد ویژه (هدر)',
                'description' => 'لینک فروش ویژه',
                'is_active' => true,
            ],
        );

        $cta = Menu::query()->where('slug', 'header_cta')->first();
        if ($cta) {
            $cta->items()->delete();
            MenuItem::query()->create([
                'menu_id' => $cta->id,
                'label' => 'فروش ویژه',
                'item_type' => 'page',
                'page_key' => 'products_special',
                'sort_order' => 0,
                'is_active' => true,
            ]);
        }
    }

    /**
     * @return list<array{label: string, item_type: string, category_slug?: string|null, page_key?: string|null, custom_url?: string|null}>
     */
    private function menuRows(): array
    {
        return [
            ['label' => 'لباس زنانه', 'item_type' => 'category', 'category_slug' => 'women'],
            ['label' => 'لباس مردانه', 'item_type' => 'category', 'category_slug' => 'men'],
            ['label' => 'جین', 'item_type' => 'category', 'category_slug' => 'jeans'],
            ['label' => 'تیشرت', 'item_type' => 'category', 'category_slug' => 'tshirts'],
            ['label' => 'شلوار', 'item_type' => 'category', 'category_slug' => 'pants'],
            ['label' => 'مانتو و پیراهن', 'item_type' => 'category', 'category_slug' => 'dress-coat'],
            ['label' => 'بافت و پلیور', 'item_type' => 'category', 'category_slug' => 'knitwear'],
            ['label' => 'کیف و کفش', 'item_type' => 'category', 'category_slug' => 'bags-shoes'],
            ['label' => 'ست راحتی', 'item_type' => 'category', 'category_slug' => 'sets'],
            ['label' => 'اکسسوری', 'item_type' => 'category', 'category_slug' => 'accessories'],
            ['label' => 'همه محصولات', 'item_type' => 'page', 'page_key' => 'products'],
        ];
    }
}