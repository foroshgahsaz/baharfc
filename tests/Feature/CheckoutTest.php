<?php

namespace Tests\Feature;

use App\Actions\Orders\CreateOrderFromCart;
use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Database\Seeders\CartCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CartCatalogSeeder::class);
    }

    public function test_checkout_requires_auth(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_order_from_cart(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create(['price' => 500000]);
        $address = Address::query()->create([
            'user_id' => $user->id,
            'title' => 'منزل',
            'recipient_name' => $user->name,
            'phone' => '09121111111',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'test',
            'is_default' => true,
        ]);

        $this->actingAs($user);
        $cart = app(CartService::class);
        $cart->add($product, 2);

        $order = app(CreateOrderFromCart::class)->execute($user, $address);

        $this->assertSame(OrderStatus::PendingPayment, $order->status);
        $this->assertSame(1_000_000, $order->subtotal);
        $this->assertCount(1, $order->items);
        $this->assertSame(0, $cart->itemCount());
    }
}
