<?php

namespace Tests\Feature;

use App\Enums\HomeCarouselStyle;
use App\Enums\HomeProductSource;
use App\Enums\HomeSectionAnchor;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeConfigurableCarouselTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_carousel_renders_after_chosen_anchor(): void
    {
        $category = Category::query()->create([
            'name' => 'مبلمان تست',
            'slug' => 'sofa-test',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        HomeSection::query()->create([
            'title' => 'آخرین مبل های ایرانی',
            'style' => HomeCarouselStyle::Style1,
            'source' => HomeProductSource::Category,
            'category_id' => $category->id,
            'product_limit' => 5,
            'position_after' => HomeSectionAnchor::SpecialDeals,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $deals = strpos($html, 'amazing-section');
        $carousel = strpos($html, 'home-carousel-style-1');

        $this->assertStringContainsString('آخرین مبل های ایرانی', $html);
        $this->assertNotFalse($carousel);

        if ($deals !== false) {
            $this->assertLessThan($carousel, $deals);
        }
    }

    public function test_style_two_renders_with_manual_products(): void
    {
        $products = Product::factory()->count(2)->create(['is_active' => true]);

        $section = HomeSection::query()->create([
            'title' => 'بهترین برندهای ایران',
            'subtitle' => 'برندهای',
            'style' => HomeCarouselStyle::Style2,
            'source' => HomeProductSource::Manual,
            'position_after' => HomeSectionAnchor::Featured,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        foreach ($products as $index => $product) {
            $section->products()->attach($product->id, ['sort_order' => $index]);
        }

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringContainsString('home-carousel-style-2', $html);
        $this->assertStringContainsString('بهترین برندهای ایران', $html);
        $this->assertStringContainsString($products->first()->name, $html);
    }
}