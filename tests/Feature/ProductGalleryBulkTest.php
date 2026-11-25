<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductGalleryBulkTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_store_multiple_gallery_paths(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        foreach (['gallery/one.jpg', 'gallery/two.jpg', 'gallery/three.jpg'] as $index => $path) {
            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $path,
                'sort_order' => $index,
            ]);
        }

        $product->refresh()->load('images');

        $this->assertCount(3, $product->images);
        $this->assertSame(['gallery/one.jpg', 'gallery/two.jpg', 'gallery/three.jpg'], $product->images->pluck('path')->all());
    }
}
