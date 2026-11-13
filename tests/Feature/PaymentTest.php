<?php

namespace Tests\Feature;

use App\Actions\Orders\CreateOrderFromCart;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\Payment\PaymentService;
use Database\Seeders\CartCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CartCatalogSeeder::class);
        config(['payment.mode' => 'sandbox']);
    }

    public function test_sandbox_payment_marks_order_paid(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create(['price' => 300000]);
        $address = Address::query()->create([
            'user_id' => $user->id,
            'title' => 'منزل',
            'recipient_name' => $user->name,
            'phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'test',
        ]);

        $this->actingAs($user);
        app(CartService::class)->add($product);
        $order = app(CreateOrderFromCart::class)->execute($user, $address);

        $payments = app(PaymentService::class);
        $payment = $payments->createPayment($order, 'mellat');
        $init = $payments->start($payment);

        $response = $this->get($init->redirectUrl);

        $response->assertRedirect(route('checkout.payment', $order));
        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
    }

    public function test_invalid_signature_fails_payment(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $product = Product::factory()->create();
        $address = Address::query()->create([
            'user_id' => $user->id,
            'title' => 'x',
            'recipient_name' => 'a',
            'phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'a',
        ]);

        $this->actingAs($user);
        app(CartService::class)->add($product);
        $order = app(CreateOrderFromCart::class)->execute($user, $address);
        $payment = app(PaymentService::class)->createPayment($order, 'parsian');

        $this->get(route('payment.callback', [
            'gateway' => 'parsian',
            'payment' => $payment->id,
            'status' => 'success',
        ]))->assertRedirect();

        $this->assertSame(PaymentStatus::Failed, $payment->fresh()->status);
    }
}
