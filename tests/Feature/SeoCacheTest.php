<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_lists_active_product(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee($product->slug, false);
    }

    public function test_guest_product_page_cache_header(): void
    {
        Cache::flush();
        $product = Product::factory()->create(['is_active' => true]);

        $first = $this->get(route('products.show', $product->slug));
        $first->assertOk();
        $this->assertSame('MISS', $first->headers->get('X-Page-Cache'));

        $second = $this->get(route('products.show', $product->slug));
        $second->assertOk();
        $this->assertSame('HIT', $second->headers->get('X-Page-Cache'));
    }

    public function test_product_page_includes_json_ld(): void
    {
        $product = Product::factory()->create(['is_active' => true, 'name' => 'محصول تست SEO']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('application/ld+json', false)
            ->assertSee('محصول تست SEO', false);
    }
}
