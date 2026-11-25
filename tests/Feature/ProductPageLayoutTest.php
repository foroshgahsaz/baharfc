<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPageLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_renders_fama_style_layout(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'slug' => 'fama-layout-product',
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('fs-product-hero-shell', false)
            ->assertSee('fs-product-tabnav', false)
            ->assertSee('fs-product-cart-row', false)
            ->assertSee('data-product-gallery', false)
            ->assertSee('معرفی محصول', false);
    }
}
