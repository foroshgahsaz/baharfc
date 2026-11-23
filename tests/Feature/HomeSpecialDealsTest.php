<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSpecialDealsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_special_deals_below_categories(): void
    {
        Product::factory()->create([
            'name' => 'محصول تخفیف‌دار تست',
            'slug' => 'discounted-test',
            'price' => 7_290_000,
            'compare_at_price' => 8_380_000,
            'is_active' => true,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $categories = strpos($html, 'fs-category-carousel');
        $deals = strpos($html, 'amazing-section');

        $this->assertNotFalse($categories);
        $this->assertNotFalse($deals);
        $this->assertLessThan($deals, $categories);

        $this->assertStringContainsString('data-amazing-deals', $html);
        $this->assertStringContainsString('پیشنهاد', $html);
        $this->assertStringContainsString('شگفت انگیز', $html);
        $this->assertStringContainsString('محصول تخفیف‌دار تست', $html);
        $this->assertStringContainsString('amazing-product-card', $html);
    }

    public function test_special_deals_hidden_when_no_discounted_products(): void
    {
        Product::factory()->create([
            'price' => 1_000_000,
            'compare_at_price' => null,
            'is_active' => true,
        ]);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('amazing-section', $html);
    }
}