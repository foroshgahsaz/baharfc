<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Product;
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

    public function test_seeder_does_not_add_template_demo_pages(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(ShopMenuSeeder::class);

        $labels = MenuItem::query()->pluck('label')->all();

        $this->assertNotContains('صفحه اصلی', $labels);
        $this->assertNotContains('صفحات قالب', $labels);
        $this->assertContains('زنانه', $labels);
        $this->assertContains('تی شرت', $labels);
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
        $response->assertSee('تی شرت', false);
        $response->assertDontSee('صفحات قالب', false);
    }

    public function test_header_matches_admin_menu_on_home_category_and_product(): void
    {
        $this->seed(DemoCatalogSeeder::class);
        $this->seed(ShopMenuSeeder::class);
        ShopMenu::forgetCache();

        $product = Product::query()->where('is_active', true)->first();
        $this->assertNotNull($product);

        foreach ([
            $this->get(route('home')),
            $this->get(route('products.index', ['category' => 'women'])),
            $this->get(route('products.show', $product->slug)),
        ] as $response) {
            $response->assertOk();
            $response->assertSee('زنانه', false);
            $response->assertSee('تی شرت', false);
            $response->assertDontSee('صفحات قالب', false);
        }
    }

    public function test_empty_menu_does_not_inject_template_pages(): void
    {
        Category::query()->create([
            'name' => 'زنانه',
            'slug' => 'women',
            'is_active' => true,
        ]);
        $product = Product::factory()->create(['is_active' => true]);

        foreach ([
            $this->get(route('home')),
            $this->get(route('products.index', ['category' => 'women'])),
            $this->get(route('products.show', $product->slug)),
        ] as $response) {
            $response->assertOk();
            $response->assertDontSee('صفحات قالب', false);
        }
    }

    public function test_header_renders_child_menu_items(): void
    {
        $menu = Menu::query()->create([
            'name' => 'منوی اصلی هدر',
            'slug' => 'header_main',
            'is_active' => true,
        ]);

        $parent = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'زنانه',
            'item_type' => 'page',
            'page_key' => 'products',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'label' => 'مانتو',
            'item_type' => 'page',
            'page_key' => 'products',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        ShopMenu::forgetCache();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('زنانه', false)
            ->assertSee('مانتو', false);
    }

    public function test_updating_menu_item_busts_guest_product_page_cache(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $menu = Menu::query()->create([
            'name' => 'منوی اصلی هدر',
            'slug' => 'header_main',
            'is_active' => true,
        ]);
        $item = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'لیبل-کش-یک',
            'item_type' => 'page',
            'page_key' => 'home',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        ShopMenu::forgetCache();

        $first = $this->get(route('products.show', $product->slug));
        $first->assertOk();
        $first->assertSee('لیبل-کش-یک', false);
        $this->assertSame('MISS', $first->headers->get('X-Page-Cache'));

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertHeader('X-Page-Cache', 'HIT');

        $item->update(['label' => 'لیبل-کش-دو']);

        $after = $this->get(route('products.show', $product->slug));
        $after->assertOk();
        $this->assertSame('MISS', $after->headers->get('X-Page-Cache'));
        $after->assertSee('لیبل-کش-دو', false);
        $after->assertDontSee('لیبل-کش-یک', false);
    }
}
