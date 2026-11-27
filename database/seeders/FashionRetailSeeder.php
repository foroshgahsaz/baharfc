<?php

namespace Database\Seeders;

use App\Enums\HomeCarouselStyle;
use App\Enums\HomeProductSource;
use App\Enums\HomeSectionAnchor;
use App\Enums\ProductType;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\WishlistItem;
use Database\Seeders\Support\SeederMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class FashionRetailSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearCatalog();

        $brands = $this->seedBrands();
        $categories = $this->seedCategories();
        $this->seedProducts($categories, $brands);
        $this->seedBanners();
        $this->seedHomeCarousels($categories);

        $this->call(FashionShopMenuSeeder::class);
    }

    private function clearCatalog(): void
    {
        if (Schema::hasTable('home_section_product')) {
            DB::table('home_section_product')->delete();
        }
        if (Schema::hasTable('home_sections')) {
            HomeSection::query()->delete();
        }
        if (Schema::hasTable('cart_items')) {
            CartItem::query()->delete();
        }
        if (Schema::hasTable('wishlist_items')) {
            WishlistItem::query()->delete();
        }
        if (Schema::hasTable('product_images')) {
            ProductImage::query()->delete();
        }
        if (Schema::hasTable('product_variants')) {
            ProductVariant::query()->delete();
        }
        if (Schema::hasTable('products')) {
            Product::query()->delete();
        }
        if (Schema::hasTable('banners')) {
            Banner::query()->delete();
        }
        if (Schema::hasTable('menu_items') && Schema::hasTable('menus')) {
            $menuIds = DB::table('menus')->whereIn('slug', ['header_main', 'header_cta'])->pluck('id');
            if ($menuIds->isNotEmpty()) {
                DB::table('menu_items')->whereIn('menu_id', $menuIds)->delete();
            }
        }
        if (Schema::hasTable('categories')) {
            Category::query()->whereNotNull('parent_id')->delete();
            Category::query()->delete();
        }
        if (Schema::hasTable('brands')) {
            Brand::query()->delete();
        }
    }

    /**
     * @return array<string, Brand>
     */
    private function seedBrands(): array
    {
        $rows = [
            ['slug' => 'hiba-style', 'name' => 'هیبا استایل', 'description' => 'الهام از هیبا استایل — مانتو، بلوز و ست‌های زنانه'],
            ['slug' => 'ibolak', 'name' => 'آی‌بولک', 'description' => 'الهام از آی‌بولک — پیراهن، شلوار جین و کاورهای زمستانی'],
            ['slug' => 'bahar', 'name' => 'بهار', 'description' => 'برند اختصاصی فروشگاه بهار'],
        ];

        $brands = [];
        foreach ($rows as $row) {
            $brands[$row['slug']] = Brand::query()->create([
                'name' => $row['name'],
                'slug' => $row['slug'],
                'description' => $row['description'],
                'is_active' => true,
            ]);
        }

        return $brands;
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $definitions = [
            ['slug' => 'women', 'name' => 'لباس زنانه', 'home' => true, 'caption' => 'جدیدترین مانتو و بلوز'],
            ['slug' => 'men', 'name' => 'لباس مردانه', 'home' => true, 'caption' => 'پیراهن و پافر مردانه'],
            ['slug' => 'jeans', 'name' => 'جین', 'home' => true, 'caption' => 'شلوار جین زنانه و مردانه'],
            ['slug' => 'tshirts', 'name' => 'تیشرت', 'home' => true, 'caption' => 'تیشرت بیسیک و طرح‌دار'],
            ['slug' => 'pants', 'name' => 'شلوار', 'home' => true, 'caption' => 'لگ، بگ و شلوار راحتی'],
            ['slug' => 'dress-coat', 'name' => 'مانتو و پیراهن', 'home' => true, 'caption' => 'مانتو، پیراهن و کت'],
            ['slug' => 'knitwear', 'name' => 'بافت و پلیور', 'home' => false, 'caption' => null],
            ['slug' => 'bags-shoes', 'name' => 'کیف و کفش', 'home' => true, 'caption' => 'کیف چرم و کتانی'],
            ['slug' => 'sets', 'name' => 'ست راحتی', 'home' => false, 'caption' => null],
            ['slug' => 'accessories', 'name' => 'اکسسوری', 'home' => true, 'caption' => 'کلیپس، اسکرانچی و کمربند'],
        ];

        $categories = [];
        foreach ($definitions as $i => $def) {
            $imagePath = SeederMedia::publishFashionImage(
                $def['slug'] === 'bags-shoes' ? 'bag-leather' : ($def['slug'] === 'men' ? 'men-jacket' : 'women-coat'),
                'categories/seed/'.$def['slug'].'.jpg',
            );

            $categories[$def['slug']] = Category::query()->create([
                'name' => $def['name'],
                'slug' => $def['slug'],
                'description' => 'دسته «'.$def['name'].'» — نمونه کاتالوگ پوشاک.',
                'image_path' => $imagePath,
                'sort_order' => $i + 1,
                'is_active' => true,
                'show_on_home' => $def['home'],
                'home_caption' => $def['caption'],
            ]);
        }

        return $categories;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array<string, Brand>  $brands
     */
    private function seedProducts(array $categories, array $brands): void
    {
        $catalog = $this->productCatalog();

        foreach ($catalog as $i => $row) {
            $category = $categories[$row['cat']] ?? null;
            $brand = $brands[$row['brand']] ?? $brands['bahar'];

            $imagePath = SeederMedia::publishFashionImage(
                $row['image'],
                SeederMedia::productImagePath($row['slug'], $row['image']),
            );

            $type = ($row['variants'] ?? []) !== [] ? ProductType::Variable : ProductType::Simple;
            $basePrice = $row['price'];
            if ($type === ProductType::Variable && isset($row['variants'][0]['price'])) {
                $basePrice = min(array_column($row['variants'], 'price'));
            }

            $product = Product::query()->create([
                'name' => $row['name'],
                'slug' => $row['slug'],
                'type' => $type,
                'category_id' => $category?->id,
                'brand_id' => $brand->id,
                'price' => $basePrice,
                'compare_at_price' => $row['compare'] ?? null,
                'short_description' => $row['short'] ?? $row['name'],
                'description' => $this->productDescription($row['name']),
                'image_path' => $imagePath,
                'is_active' => true,
                'is_featured' => (bool) ($row['featured'] ?? $i < 12),
                'sort_order' => $i + 1,
            ]);

            if ($imagePath !== null) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $imagePath,
                    'alt' => $row['name'],
                    'sort_order' => 0,
                ]);
            }

            $secondary = SeederMedia::publishFashionImage(
                $row['image_alt'] ?? 'sport-set',
                SeederMedia::productImagePath($row['slug'].'-alt', $row['image_alt'] ?? 'sport-set'),
            );
            if ($secondary !== null) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $secondary,
                    'alt' => $row['name'].' — نمای دوم',
                    'sort_order' => 1,
                ]);
            }

            foreach ($row['variants'] ?? [] as $vIndex => $variant) {
                ProductVariant::query()->create([
                    'product_id' => $product->id,
                    'name' => $variant['name'],
                    'sku' => $variant['sku'],
                    'options' => $variant['options'],
                    'price' => $variant['price'],
                    'compare_at_price' => $variant['compare'] ?? null,
                    'stock' => 25,
                    'is_active' => true,
                    'sort_order' => $vIndex,
                ]);
            }
        }
    }

    private function seedBanners(): void
    {
        $slides = [
            ['title' => 'کالکشن پاییز', 'subtitle' => 'مانتو، پافر و بافت — تا ۵۰٪ تخفیف', 'image' => 'banner-fashion', 'slug' => 'fall-collection'],
            ['title' => 'لباس مردانه', 'subtitle' => 'پیراهن لینن و شلوار جین', 'image' => 'men-shirt', 'slug' => 'men-collection'],
            ['title' => 'اکسسوری ترند', 'subtitle' => 'کیف، اسکرانچی و کمربند', 'image' => 'accessory', 'slug' => 'accessories-banner'],
        ];

        foreach ($slides as $i => $slide) {
            $path = SeederMedia::publishFashionImage($slide['image'], SeederMedia::bannerImagePath($slide['slug']));

            Banner::query()->create([
                'title' => $slide['title'],
                'subtitle' => $slide['subtitle'],
                'image_path' => $path,
                'image_url' => null,
                'link_url' => route('products.index'),
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }

    /**
     * @param  array<string, Category>  $categories
     */
    private function seedHomeCarousels(array $categories): void
    {
        if (! Schema::hasTable('home_sections')) {
            return;
        }

        $womenJeans = Product::query()
            ->whereIn('slug', ['women-jean-loope', 'women-leg-flament', 'men-linen-polo'])
            ->pluck('id')
            ->all();

        $sectionManual = HomeSection::query()->create([
            'title' => 'منتخب هیبا استایل',
            'subtitle' => 'زنانه',
            'style' => HomeCarouselStyle::Style2,
            'source' => HomeProductSource::Manual,
            'position_after' => HomeSectionAnchor::SpecialDeals,
            'sort_order' => 0,
            'is_active' => true,
            'more_label' => 'مشاهده همه',
        ]);

        foreach (array_values($womenJeans) as $sort => $productId) {
            $sectionManual->products()->attach($productId, ['sort_order' => $sort]);
        }

        HomeSection::query()->create([
            'title' => 'شلوار و جین آی‌بولک',
            'subtitle' => 'پرفروش',
            'style' => HomeCarouselStyle::Style1,
            'source' => HomeProductSource::Category,
            'category_id' => $categories['jeans']->id,
            'product_limit' => 8,
            'position_after' => HomeSectionAnchor::SpecialDeals,
            'sort_order' => 1,
            'is_active' => true,
        ]);
    }

    private function productDescription(string $name): string
    {
        return '<p><strong>'.e($name).'</strong></p>'
            .'<p>این توضیح نمونه برای دمو فروشگاه است؛ الگوی عرضه از فروشگاه‌های مد روز (هیبا استایل و آی‌بولک) گرفته شده است.</p>'
            .'<ul><li>جنس با کیفیت و دوخت تمیز</li><li>ارسال سریع به سراسر کشور</li><li>۷ روز مهلت بازگشت طبق قوانین فروشگاه</li></ul>';
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function productCatalog(): array
    {
        return [
            ['name' => 'کت فوتر زنانه کوتاه مینیمال', 'slug' => 'women-short-coat', 'cat' => 'dress-coat', 'brand' => 'ibolak', 'price' => 3_750_000, 'compare' => 6_250_000, 'image' => 'women-coat', 'featured' => true],
            ['name' => 'مانتو کژوال زنانه', 'slug' => 'women-manto-casual', 'cat' => 'women', 'brand' => 'hiba-style', 'price' => 2_890_000, 'image' => 'women-dress', 'featured' => true],
            ['name' => 'شلوار جین لوپ لایت سرمه‌ای', 'slug' => 'women-jean-loope', 'cat' => 'jeans', 'brand' => 'ibolak', 'price' => 4_529_000, 'image' => 'women-jeans'],
            ['name' => 'لگ زنانه فلامنت دمپا', 'slug' => 'women-leg-flament', 'cat' => 'pants', 'brand' => 'hiba-style', 'price' => 1_829_000, 'image' => 'sport-set'],
            ['name' => 'شلوار زنانه بگ کبریتی', 'slug' => 'women-baggy-knit-pant', 'cat' => 'pants', 'brand' => 'ibolak', 'price' => 2_397_000, 'image' => 'knit'],
            ['name' => 'تیشرت زنانه بیسیک نخ پنبه', 'slug' => 'women-basic-tee', 'cat' => 'tshirts', 'brand' => 'hiba-style', 'price' => 799_000, 'compare' => 1_199_000, 'image' => 'tshirt'],
            ['name' => 'پلیور بافت زنانه', 'slug' => 'women-knit-pullover', 'cat' => 'knitwear', 'brand' => 'hiba-style', 'price' => 2_400_000, 'image' => 'knit'],
            ['name' => 'ست راحتی زنانه بانی‌ پاپ', 'slug' => 'women-lounge-set', 'cat' => 'sets', 'brand' => 'ibolak', 'price' => 3_520_800, 'compare' => 5_868_000, 'image' => 'sport-set', 'featured' => true],
            ['name' => 'کیف زنانه کتان طرح بامبی', 'slug' => 'women-cotton-bag', 'cat' => 'bags-shoes', 'brand' => 'ibolak', 'price' => 2_649_000, 'image' => 'bag-leather'],
            ['name' => 'کیف اسپرت زنانه چرم', 'slug' => 'women-leather-sport-bag', 'cat' => 'bags-shoes', 'brand' => 'ibolak', 'price' => 1_239_000, 'image' => 'bag-leather'],
            ['name' => 'کلیپس زنانه طرح گل', 'slug' => 'hair-clip-flower', 'cat' => 'accessories', 'brand' => 'hiba-style', 'price' => 299_000, 'compare' => 399_000, 'image' => 'accessory'],
            ['name' => 'اسکرانچی طرح پاپیون', 'slug' => 'scrunchie-bow', 'cat' => 'accessories', 'brand' => 'hiba-style', 'price' => 119_700, 'compare' => 399_000, 'image' => 'accessory'],
            ['name' => 'پیراهن مردانه لینن پولو', 'slug' => 'men-linen-polo', 'cat' => 'men', 'brand' => 'ibolak', 'price' => 1_499_000, 'compare' => 2_998_000, 'image' => 'men-shirt', 'featured' => true],
            ['name' => 'پافر مردانه دو جیب', 'slug' => 'men-puffer-2pocket', 'cat' => 'men', 'brand' => 'ibolak', 'price' => 3_750_000, 'compare' => 6_250_000, 'image' => 'men-jacket', 'featured' => true],
            ['name' => 'تیشرت مردانه نخی', 'slug' => 'men-cotton-tee', 'cat' => 'tshirts', 'brand' => 'bahar', 'price' => 1_099_000, 'compare' => 2_198_000, 'image' => 'tshirt'],
            ['name' => 'شلوار جین مردانه اسلیم', 'slug' => 'men-slim-jeans', 'cat' => 'jeans', 'brand' => 'ibolak', 'price' => 3_139_000, 'image' => 'women-jeans', 'image_alt' => 'men-jacket'],
            ['name' => 'کتانی ورزشی مردانه', 'slug' => 'men-running-shoes', 'cat' => 'bags-shoes', 'brand' => 'bahar', 'price' => 2_250_000, 'image' => 'men-sneakers',
                'variants' => [
                    ['name' => 'سایز ۴۰', 'sku' => 'MSHOE-40', 'price' => 2_100_000, 'options' => ['size' => '40', 'color' => 'سفید']],
                    ['name' => 'سایز ۴۲', 'sku' => 'MSHOE-42', 'price' => 2_250_000, 'options' => ['size' => '42', 'color' => 'سفید']],
                    ['name' => 'سایز ۴۴', 'sku' => 'MSHOE-44', 'price' => 2_300_000, 'options' => ['size' => '44', 'color' => 'سرمه‌ای']],
                ],
            ],
            ['name' => 'هودی اسپرت مردانه', 'slug' => 'men-hoodie', 'cat' => 'men', 'brand' => 'hiba-style', 'price' => 1_950_000, 'compare' => 2_600_000, 'image' => 'men-jacket'],
            ['name' => 'ست تیشرت و شلوار مردانه', 'slug' => 'men-tee-pant-set', 'cat' => 'sets', 'brand' => 'ibolak', 'price' => 2_519_000, 'image' => 'sport-set'],
            ['name' => 'شلوار راحتی مردانه', 'slug' => 'men-jogger', 'cat' => 'pants', 'brand' => 'bahar', 'price' => 1_749_000, 'compare' => 3_498_000, 'image' => 'knit'],
            ['name' => 'پیراهن مردانه آستین بلند', 'slug' => 'men-dress-shirt', 'cat' => 'dress-coat', 'brand' => 'ibolak', 'price' => 2_049_000, 'image' => 'men-shirt'],
            ['name' => 'تیشرت زنانه لانگ', 'slug' => 'women-long-tee', 'cat' => 'tshirts', 'brand' => 'hiba-style', 'price' => 2_049_000, 'image' => 'tshirt',
                'variants' => [
                    ['name' => 'سایز S', 'sku' => 'WLTEE-S', 'price' => 1_949_000, 'options' => ['size' => 'S']],
                    ['name' => 'سایز M', 'sku' => 'WLTEE-M', 'price' => 2_049_000, 'options' => ['size' => 'M']],
                    ['name' => 'سایز L', 'sku' => 'WLTEE-L', 'price' => 2_149_000, 'options' => ['size' => 'L']],
                ],
            ],
            ['name' => 'شلوار زنانه بگ سورن', 'slug' => 'women-fendi-baggy', 'cat' => 'pants', 'brand' => 'ibolak', 'price' => 2_603_700, 'compare' => 2_893_000, 'image' => 'women-jeans'],
            ['name' => 'دامن جین زنانه', 'slug' => 'women-denim-skirt', 'cat' => 'jeans', 'brand' => 'hiba-style', 'price' => 2_349_000, 'image' => 'women-jeans'],
            ['name' => 'کاردیگن بافت زنانه', 'slug' => 'women-cardigan', 'cat' => 'knitwear', 'brand' => 'hiba-style', 'price' => 2_899_000, 'image' => 'knit'],
            ['name' => 'کمربند چرم زنانه', 'slug' => 'women-leather-belt', 'cat' => 'accessories', 'brand' => 'hiba-style', 'price' => 459_000, 'image' => 'accessory'],
            ['name' => 'کفش کژوال زنانه', 'slug' => 'women-casual-shoes', 'cat' => 'bags-shoes', 'brand' => 'bahar', 'price' => 3_589_000, 'image' => 'men-sneakers', 'image_alt' => 'women-dress'],
            ['name' => 'ست بلوز و شلوار زنانه', 'slug' => 'women-blouse-pant-set', 'cat' => 'sets', 'brand' => 'hiba-style', 'price' => 3_977_000, 'image' => 'women-dress'],
            ['name' => 'شومیز زنانه مجلسی', 'slug' => 'women-formal-blouse', 'cat' => 'women', 'brand' => 'hiba-style', 'price' => 2_888_000, 'image' => 'women-dress'],
            ['name' => 'پالتو زنانه پشمی', 'slug' => 'women-wool-coat', 'cat' => 'dress-coat', 'brand' => 'ibolak', 'price' => 5_200_000, 'compare' => 6_500_000, 'image' => 'women-coat'],
            ['name' => 'ژاکت جین مردانه', 'slug' => 'men-denim-jacket', 'cat' => 'men', 'brand' => 'ibolak', 'price' => 2_384_100, 'compare' => 2_649_000, 'image' => 'men-jacket'],
        ];
    }
}