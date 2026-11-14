<?php

namespace Tests\Feature;

use App\Enums\CouponType;
use App\Livewire\Shop\AddToCart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Database\Seeders\CartCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CartCatalogSeeder::class);
    }

    public function test_guest_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create();

        $cart = app(CartService::class);
        $cart->add($product, 2);

        $this->assertSame(2, $cart->itemCount());
        $this->assertSame($product->price * 2, $cart->subtotal());
    }

    public function test_authenticated_cart_persists_in_database(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();

        $this->actingAs($user);

        app(CartService::class)->add($product);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_coupon_discount_is_applied(): void
    {
        Coupon::query()->create([
            'code' => 'TEST50',
            'type' => CouponType::Fixed,
            'value' => 50000,
            'min_order_amount' => 0,
            'is_active' => true,
        ]);

        $product = Product::factory()->create(['price' => 200000]);
        $cart = app(CartService::class);
        $cart->add($product);
        $cart->applyCoupon('TEST50');

        $this->assertSame(50000, $cart->discountTotal());
        $this->assertSame(150000 + $cart->shippingTotal(), $cart->grandTotal());
    }

    public function test_cart_page_renders(): void
    {
        $this->get(route('cart.index'))->assertOk();
    }

    public function test_add_to_cart_livewire_on_product_page(): void
    {
        $product = Product::factory()->create();

        Livewire::test(AddToCart::class, ['productId' => $product->id])
            ->call('add')
            ->assertHasNoErrors();

        $this->assertSame(1, app(CartService::class)->itemCount());
    }
}
