<?php

namespace Database\Seeders;

use App\Enums\ProductType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::query()->updateOrCreate(
            ['slug' => 'bahar'],
            ['name' => 'بهر', 'description' => 'برند اختصاصی فروشگاه', 'is_active' => true],
        );

        Brand::query()->updateOrCreate(['slug' => 'noa'], ['name' => 'نوآ', 'is_active' => true]);
        Brand::query()->updateOrCreate(['slug' => 'ariana'], ['name' => 'آرینا', 'is_active' => true]);

        $tree = [
            ['name' => 'پوشاک', 'slug' => 'clothing', 'children' => [
                ['name' => 'مردانه', 'slug' => 'men'],
                ['name' => 'زنانه', 'slug' => 'women'],
                ['name' => 'بچگانه', 'slug' => 'kids'],
            ]],
            ['name' => 'کفش و کیف', 'slug' => 'footwear-bags', 'children' => [
                ['name' => 'کفش ورزشی', 'slug' => 'sport-shoes-cat'],
                ['name' => 'کیف و کوله', 'slug' => 'bags'],
            ]],
            ['name' => 'دیجیتال', 'slug' => 'digital', 'children' => [
                ['name' => 'موبایل و تبلت', 'slug' => 'mobile-tablet'],
                ['name' => 'لوازم جانبی', 'slug' => 'accessories'],
            ]],
            ['name' => 'خانه و آشپزخانه', 'slug' => 'home-kitchen', 'children' => []],
            ['name' => 'آرایشی و بهداشتی', 'slug' => 'beauty', 'children' => []],
            ['name' => 'ورزش و سفر', 'slug' => 'sport-travel', 'children' => []],
        ];

        $categories = [];
        $order = 0;
        foreach ($tree as $root) {
            $order++;
            $parent = Category::query()->updateOrCreate(
                ['slug' => $root['slug']],
                ['name' => $root['name'], 'is_active' => true, 'sort_order' => $order],
            );
            $categories[$root['slug']] = $parent;

            $childOrder = 0;
            foreach ($root['children'] ?? [] as $child) {
                $childOrder++;
                $cat = Category::query()->updateOrCreate(
                    ['slug' => $child['slug']],
                    [
                        'name' => $child['name'],
                        'parent_id' => $parent->id,
                        'is_active' => true,
                        'sort_order' => $childOrder,
                    ],
                );
                $categories[$child['slug']] = $cat;
            }
        }

        $this->seedSimpleProducts($categories, $brand);
        $this->seedVariableProducts($categories, $brand);
    }

    private function seedSimpleProducts(array $categories, Brand $brand): void
    {
        $simples = [
            ['name' => 'تی‌شرت نخی مردانه', 'slug' => 'mens-cotton-tee', 'cat' => 'men', 'price' => 389_000, 'compare' => 450_000],
            ['name' => 'مانتو کژوال زنانه', 'slug' => 'women-manto', 'cat' => 'women', 'price' => 1_290_000, 'compare' => null],
            ['name' => 'هدفون بلوتوثی', 'slug' => 'bt-headphone', 'cat' => 'accessories', 'price' => 890_000, 'compare' => 1_050_000],
            ['name' => 'چای‌ساز برقی', 'slug' => 'electric-kettle', 'cat' => 'home-kitchen', 'price' => 2_100_000, 'compare' => null],
            ['name' => 'کرم آبرسان صورت', 'slug' => 'face-moisturizer', 'cat' => 'beauty', 'price' => 320_000, 'compare' => 380_000],
            ['name' => 'کیف مسافرتی', 'slug' => 'travel-bag', 'cat' => 'sport-travel', 'price' => 1_750_000, 'compare' => null],
            ['name' => 'کتاب آشپزی ایرانی', 'slug' => 'cookbook', 'cat' => 'home-kitchen', 'price' => 185_000, 'compare' => null],
            ['name' => 'پاوربانک ۲۰۰۰۰', 'slug' => 'powerbank-20k', 'cat' => 'accessories', 'price' => 1_120_000, 'compare' => 1_400_000],
        ];

        foreach ($simples as $i => $row) {
            Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'type' => ProductType::Simple,
                    'category_id' => $categories[$row['cat']]->id ?? null,
                    'brand_id' => $brand->id,
                    'price' => $row['price'],
                    'compare_at_price' => $row['compare'],
                    'short_description' => 'محصول تستی — '.$row['name'],
                    'is_active' => true,
                    'is_featured' => $i < 4,
                    'sort_order' => $i + 1,
                ],
            );
        }
    }

    private function seedVariableProducts(array $categories, Brand $brand): void
    {
        $variables = [
            [
                'name' => 'کتانی ورزشی مردانه',
                'slug' => 'running-shoes-variable',
                'cat' => 'sport-shoes-cat',
                'variants' => [
                    ['name' => 'سایز ۴۰ — مشکی', 'sku' => 'SHOE-40-BLK', 'price' => 2_100_000, 'options' => ['size' => '40', 'color' => 'مشکی']],
                    ['name' => 'سایز ۴۲ — مشکی', 'sku' => 'SHOE-42-BLK', 'price' => 2_150_000, 'options' => ['size' => '42', 'color' => 'مشکی']],
                    ['name' => 'سایز ۴۲ — سفید', 'sku' => 'SHOE-42-WHT', 'price' => 2_250_000, 'compare' => 2_450_000, 'options' => ['size' => '42', 'color' => 'سفید']],
                    ['name' => 'سایز ۴۴ — سرمه‌ای', 'sku' => 'SHOE-44-NVY', 'price' => 2_300_000, 'options' => ['size' => '44', 'color' => 'سرمه‌ای']],
                ],
            ],
            [
                'name' => 'هودی اسپرت',
                'slug' => 'hoodie-variable',
                'cat' => 'men',
                'variants' => [
                    ['name' => 'M — خاکستری', 'sku' => 'HOOD-M-GRY', 'price' => 780_000, 'options' => ['size' => 'M', 'color' => 'خاکستری']],
                    ['name' => 'L — خاکستری', 'sku' => 'HOOD-L-GRY', 'price' => 790_000, 'options' => ['size' => 'L', 'color' => 'خاکستری']],
                    ['name' => 'XL — مشکی', 'sku' => 'HOOD-XL-BLK', 'price' => 820_000, 'options' => ['size' => 'XL', 'color' => 'مشکی']],
                ],
            ],
            [
                'name' => 'گوشی هوشمند (حافظه متفاوت)',
                'slug' => 'smartphone-variable',
                'cat' => 'mobile-tablet',
                'variants' => [
                    ['name' => '۱۲۸ گیگ — مشکی', 'sku' => 'PHONE-128-BLK', 'price' => 18_900_000, 'compare' => 19_500_000, 'options' => ['storage' => '128GB']],
                    ['name' => '۲۵۶ گیگ — مشکی', 'sku' => 'PHONE-256-BLK', 'price' => 21_400_000, 'options' => ['storage' => '256GB']],
                    ['name' => '۵۱۲ گیگ — سفید', 'sku' => 'PHONE-512-WHT', 'price' => 24_900_000, 'options' => ['storage' => '512GB']],
                ],
            ],
            [
                'name' => 'عطر ادکلن',
                'slug' => 'perfume-variable',
                'cat' => 'beauty',
                'variants' => [
                    ['name' => '۵۰ میلی‌لیتر', 'sku' => 'PERF-50', 'price' => 1_450_000, 'options' => ['volume' => '50ml']],
                    ['name' => '۱۰۰ میلی‌لیتر', 'sku' => 'PERF-100', 'price' => 2_350_000, 'compare' => 2_600_000, 'options' => ['volume' => '100ml']],
                ],
            ],
        ];

        foreach ($variables as $i => $item) {
            $prices = array_column($item['variants'], 'price');
            $minPrice = min($prices);

            $product = Product::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'type' => ProductType::Variable,
                    'category_id' => $categories[$item['cat']]->id ?? null,
                    'brand_id' => $brand->id,
                    'price' => $minPrice,
                    'compare_at_price' => null,
                    'short_description' => 'محصول متغیر با قیمت‌های مختلف بر اساس گزینه.',
                    'is_active' => true,
                    'is_featured' => true,
                    'sort_order' => 20 + $i,
                ],
            );

            foreach ($item['variants'] as $vOrder => $variant) {
                ProductVariant::query()->updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'sku' => $variant['sku'],
                    ],
                    [
                        'name' => $variant['name'],
                        'price' => $variant['price'],
                        'compare_at_price' => $variant['compare'] ?? null,
                        'options' => $variant['options'],
                        'stock' => 50,
                        'is_active' => true,
                        'sort_order' => $vOrder,
                    ],
                );
            }
        }
    }
}
