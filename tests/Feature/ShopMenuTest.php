<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\ShopMenu;
use Database\Seeders\DemoCatalogSeeder;
use Database\Seeders\ShopMenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_header_menu(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(ShopMenuSeeder::class);

        $menu = Menu::query()->where('slug', 'header_main')->first();
        $this->assertNotNull($menu);
        $this->assertGreaterThan(0, $menu->items()->count());
    }

    public function test_header_items_resolves_category_url(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(ShopMenuSeeder::class);

        ShopMenu::forgetCache();

        $items = ShopMenu::headerItems();
        $categoryItem = $items->first(fn (MenuItem $item) => $item->item_type === 'category');

        if ($categoryItem && $categoryItem->category) {
            $this->assertStringContainsString(
                $categoryItem->category->slug,
                $categoryItem->resolvedUrl(),
            );
        } else {
            $this->markTestSkipped('No seeded category menu item.');
        }
    }

    public function test_home_page_renders_seeded_nav_links(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(ShopMenuSeeder::class);

        ShopMenu::forgetCache();

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('صفحه اصلی', false);
    }
}