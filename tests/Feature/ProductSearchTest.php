<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_filters_by_search_query(): void
    {
        Product::query()->create([
            'name' => 'گوشی هوشمند آلفا',
            'slug' => 'phone-alpha',
            'price' => 1000,
            'is_active' => true,
        ]);
        Product::query()->create([
            'name' => 'کت چرمی',
            'slug' => 'coat',
            'price' => 2000,
            'is_active' => true,
        ]);

        $response = $this->get(route('products.index', ['q' => 'گوشی']));

        $response->assertOk();
        $response->assertSee('گوشی هوشمند آلفا', false);
        $response->assertDontSee('کت چرمی', false);
    }
}
