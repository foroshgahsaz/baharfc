<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class CartCatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'پست پیشتاز', 'slug' => 'express-post', 'price' => 85000, 'sort_order' => 1],
            ['name' => 'پیک موتوری (تهران)', 'slug' => 'tehran-courier', 'price' => 120000, 'sort_order' => 2],
            ['name' => 'ارسال رایگان (سفارش بالای ۲ میلیون)', 'slug' => 'free-shipping', 'price' => 0, 'sort_order' => 3],
        ] as $method) {
            ShippingMethod::query()->updateOrCreate(
                ['slug' => $method['slug']],
                array_merge($method, ['is_active' => true]),
            );
        }

        Coupon::query()->updateOrCreate(
            ['code' => 'BAHAR10'],
            [
                'type' => CouponType::Percent,
                'value' => 10,
                'min_order_amount' => 100000,
                'max_uses' => null,
                'is_active' => true,
            ],
        );
    }
}
