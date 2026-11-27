<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_index_renders_catalog_layout_and_seo_block(): void
    {
        $category = Category::query()->create([
            'name' => 'پوشاک زنانه',
            'slug' => 'women-dress',
            'description' => '<p>راهنمای خرید <strong>پوشاک زنانه</strong> با کیفیت.</p>',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Product::query()->create([
            'name' => 'مانتو مجلسی',
            'slug' => 'formal-manto',
            'price' => 1_500_000,
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $response = $this->get(route('categories.show', $category->slug));

        $response->assertOk();
        $response->assertSee('fs-catalog-page', false);
        $response->assertSee('fs-catalog-layout', false);
        $response->assertSee('fs-catalog-sidebar', false);
        $response->assertSee('پوشاک زنانه', false);
        $response->assertSee('مانتو مجلسی', false);
        $response->assertSee('راهنمای خرید', false);
        $response->assertSee('fs-catalog-card', false);
    }
}