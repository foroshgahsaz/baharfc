<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@baharfc.local'],
            [
                'name' => 'مدیر فروشگاه',
                'username' => 'admin',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        $fashion = Category::query()->updateOrCreate(
            ['slug' => 'fashion'],
            ['name' => 'مد و پوشاک', 'is_active' => true, 'sort_order' => 1],
        );

        $tech = Category::query()->updateOrCreate(
            ['slug' => 'tech'],
            ['name' => 'دیجیتال', 'is_active' => true, 'sort_order' => 2],
        );

        $brandPremium = Brand::query()->updateOrCreate(
            ['slug' => 'bahar'],
            ['name' => 'بهر', 'is_active' => true],
        );

        $samples = [
            ['name' => 'کیف چرم دست‌دوز', 'slug' => 'leather-bag', 'category_id' => $fashion->id, 'brand_id' => $brandPremium->id, 'price' => 2_450_000, 'compare_at_price' => 2_890_000, 'short_description' => 'چرم طبیعی با دوخت دستی', 'is_featured' => true, 'sort_order' => 1],
            ['name' => 'کتانی اسپرت مردانه', 'slug' => 'sport-shoes', 'category_id' => $fashion->id, 'brand_id' => $brandPremium->id, 'price' => 1_890_000, 'compare_at_price' => null, 'is_featured' => true, 'sort_order' => 2],
            ['name' => 'ساعت هوشمند', 'slug' => 'smart-watch', 'category_id' => $tech->id, 'brand_id' => $brandPremium->id, 'price' => 3_200_000, 'compare_at_price' => 3_600_000, 'is_featured' => true, 'sort_order' => 3],
            ['name' => 'هدفون بی‌سیم', 'slug' => 'wireless-headphones', 'category_id' => $tech->id, 'brand_id' => $brandPremium->id, 'price' => 1_250_000, 'compare_at_price' => null, 'is_featured' => true, 'sort_order' => 4],
        ];

        foreach ($samples as $item) {
            Product::query()->updateOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, ['is_active' => true]),
            );
        }
    }
}
