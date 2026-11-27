<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Product;
use Database\Seeders\FashionRetailSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FashionRetailSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_fashion_retail_seeder_populates_catalog_and_menu(): void
    {
        Storage::fake('public');

        $this->seed(FashionRetailSeeder::class);

        $this->assertGreaterThanOrEqual(25, Product::query()->count());
        $this->assertGreaterThanOrEqual(8, Category::query()->count());
        $this->assertGreaterThanOrEqual(8, MenuItem::query()->count());

        $this->get(route('home'))->assertOk()->assertSee('کت فوتر زنانه کوتاه مینیمال', false);
    }
}