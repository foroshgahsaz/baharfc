<?php

namespace Tests\Feature;

use App\Livewire\Shop\ProductReviews;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductUgcTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_submit_product_review(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        Livewire::test(ProductReviews::class, ['product' => $product])
            ->set('authorName', 'کاربر تست')
            ->set('authorEmail', 'test@example.com')
            ->set('rating', 4)
            ->set('body', 'کیفیت خوبی داشت.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'author_name' => 'کاربر تست',
            'status' => 'pending',
        ]);

        ProductReview::query()->first()?->update(['status' => 'approved']);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('کیفیت خوبی داشت.', false);
    }
}
