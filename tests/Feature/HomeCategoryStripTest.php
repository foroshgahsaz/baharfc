<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCategoryStripTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_category_section_below_slider(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $hero = strpos($html, 'id="homeHero"');
        $categories = strpos($html, 'home-product-categories');

        $this->assertNotFalse($hero);
        $this->assertNotFalse($categories);
        $this->assertLessThan($categories, $hero);
        $this->assertStringContainsString('دسته بندی محصولات', $html);
        $this->assertStringContainsString('مشاهده همه', $html);
        $this->assertStringNotContainsString('با جدیدها', $html);
        $this->assertStringNotContainsString('category-promo', $html);
    }

    public function test_home_categories_show_name_and_count_but_not_price(): void
    {
        $category = Category::query()->create([
            'name' => 'کفش ورزشی خانگی',
            'slug' => 'sport-shoes-home',
            'is_active' => true,
            'show_on_home' => true,
            'sort_order' => 1,
        ]);

        $product = Product::factory()->create([
            'name' => 'کتانی تست منو',
            'category_id' => $category->id,
            'price' => 4_321_000,
            'is_active' => true,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/<section class="home-product-categories".*?<\/section>/s',
            $html,
            $matches,
        ));

        $section = $matches[0];
        $this->assertStringContainsString('کفش ورزشی خانگی', $section);
        $this->assertStringContainsString('دسته بندی محصولات', $section);
        $this->assertStringContainsString('۱+', $section);
        $this->assertStringContainsString('محصول', $section);
        $this->assertStringNotContainsString('تومان', $section);
        $this->assertStringNotContainsString('4,321,000', $section);
        $this->assertStringNotContainsString($product->formattedPrice(), $section);
    }
}
