<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\ReturnRequestStatus;
use App\Livewire\Account\ReturnRequestForm;
use App\Livewire\Shop\ProductWishlistToggle;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WishlistReturnTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_wishlist_redirects_to_login(): void
    {
        $product = Product::factory()->create();

        Livewire::test(ProductWishlistToggle::class, ['productId' => $product->id])
            ->call('toggle')
            ->assertRedirect(route('login'));
    }

    public function test_user_can_toggle_wishlist(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        Livewire::test(ProductWishlistToggle::class, ['productId' => $product->id])
            ->call('toggle');

        $this->assertDatabaseHas('wishlist_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        Livewire::test(ProductWishlistToggle::class, ['productId' => $product->id])
            ->call('toggle');

        $this->assertDatabaseMissing('wishlist_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_submit_return_request(): void
    {
        $user = User::factory()->create();
        $order = Order::query()->create([
            'number' => 'BHR-TEST-01',
            'user_id' => $user->id,
            'status' => OrderStatus::Delivered,
            'subtotal' => 10000,
            'total' => 10000,
            'recipient_name' => $user->name,
            'recipient_phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'test',
        ]);

        $this->actingAs($user);

        Livewire::test(ReturnRequestForm::class)
            ->set('orderId', $order->id)
            ->set('reason', 'کالا با توضیحات مغایرت دارد و نیاز به مرجوعی دارد.')
            ->call('submit');

        $this->assertDatabaseHas('return_requests', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'status' => ReturnRequestStatus::Pending->value,
        ]);
    }
}
