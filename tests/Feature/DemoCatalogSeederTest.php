<?php

namespace Tests\Feature;

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_catalog_seeder_creates_categories_and_variable_products(): void
    {
        $this->seed(DemoCatalogSeeder::class);

        $this->assertGreaterThanOrEqual(6, Category::query()->count());
        $this->assertGreaterThanOrEqual(8, Product::query()->where('type', ProductType::Simple)->count());

        $variable = Product::query()->where('type', ProductType::Variable)->where('slug', 'running-shoes-variable')->first();
        $this->assertNotNull($variable);
        $this->assertGreaterThanOrEqual(3, ProductVariant::query()->where('product_id', $variable->id)->count());

        $prices = ProductVariant::query()->where('product_id', $variable->id)->pluck('price');
        $this->assertTrue($prices->unique()->count() > 1);
    }
}
