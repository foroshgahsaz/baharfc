<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_does_not_see_admin_bar_on_shop(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('baharfc-admin-bar', false);
    }

    public function test_admin_sees_edit_link_on_product_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('baharfc-admin-bar', false)
            ->assertSee('ویرایش محصول')
            ->assertSee('/admin/products/'.$product->id.'/edit', false);
    }

    public function test_admin_sees_admin_bar_in_filament_panel(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->get('/admin/products/'.$product->id.'/edit')
            ->assertOk()
            ->assertSee('baharfc-admin-bar', false)
            ->assertSee('مشاهده در سایت')
            ->assertSee(route('products.show', $product->slug), false);
    }

    public function test_admin_sees_blog_edit_on_post_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = BlogPost::query()->create([
            'title' => 'تست',
            'slug' => 'test-post',
            'body' => 'متن',
            'status' => 'published',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('ویرایش نوشته');
    }
}