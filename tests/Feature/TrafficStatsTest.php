<?php

namespace Tests\Feature;

use App\Enums\TrafficEntityType;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\TrafficDailyStat;
use App\Models\User;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrafficStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_records_view(): void
    {
        Product::factory()->create([
            'slug' => 'tracked-shirt',
            'is_active' => true,
        ]);

        $this->get(route('products.show', 'tracked-shirt'))->assertOk();

        $this->assertDatabaseHas('traffic_daily_stats', [
            'entity_type' => TrafficEntityType::Product->value,
            'entity_key' => 'tracked-shirt',
            'views' => 1,
        ]);
    }

    public function test_blog_post_and_home_are_tracked(): void
    {
        BlogPost::query()->create([
            'title' => 'مقاله',
            'slug' => 'stat-post',
            'body' => 'متن',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('blog.show', 'stat-post'))->assertOk();
        $this->get(route('home'))->assertOk();

        $this->assertDatabaseHas('traffic_daily_stats', [
            'entity_type' => TrafficEntityType::BlogPost->value,
            'entity_key' => 'stat-post',
        ]);

        $this->assertDatabaseHas('traffic_daily_stats', [
            'entity_type' => TrafficEntityType::Page->value,
            'entity_key' => 'home',
        ]);
    }

    public function test_category_listing_tracked_separately_from_products_index(): void
    {
        Category::query()->create([
            'name' => 'زنانه',
            'slug' => 'women',
            'is_active' => true,
        ]);

        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.index', ['category' => 'women']))->assertOk();

        $this->assertDatabaseHas('traffic_daily_stats', [
            'entity_type' => TrafficEntityType::Page->value,
            'entity_key' => 'products_index',
        ]);

        $this->assertDatabaseHas('traffic_daily_stats', [
            'entity_type' => TrafficEntityType::Category->value,
            'entity_key' => 'women',
        ]);
    }

    public function test_cached_product_page_still_records_view(): void
    {
        Product::factory()->create([
            'slug' => 'cached-product',
            'is_active' => true,
        ]);

        $first = $this->get(route('products.show', 'cached-product'))->assertOk();
        $this->assertSame('MISS', $first->headers->get('X-Page-Cache'));

        $second = $this->get(route('products.show', 'cached-product'))->assertOk();
        $this->assertSame('HIT', $second->headers->get('X-Page-Cache'));

        $views = (int) TrafficDailyStat::query()
            ->where('entity_type', TrafficEntityType::Product->value)
            ->where('entity_key', 'cached-product')
            ->value('views');

        $this->assertSame(2, $views);
    }

    public function test_shop_manager_can_open_traffic_report(): void
    {
        $this->seed(AdminRoleSeeder::class);

        $manager = User::factory()->create([
            'is_admin' => false,
            'admin_role_id' => \App\Models\AdminRole::query()->where('slug', 'shop_manager')->value('id'),
        ]);

        $this->actingAs($manager)
            ->get('/admin/traffic-statistics')
            ->assertOk();
    }
}