<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\OrderPaid;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function makeOrder(User $user, OrderStatus $status = OrderStatus::PendingPayment): Order
    {
        $order = Order::query()->create([
            'number' => 'BHR-'.random_int(1000, 9999),
            'user_id' => $user->id,
            'status' => $status,
            'subtotal' => 100000,
            'discount_total' => 0,
            'shipping_total' => 0,
            'total' => 100000,
            'recipient_name' => $user->name,
            'recipient_phone' => '09121234567',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'خیابان تست',
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => Product::factory()->create()->id,
            'product_name' => 'نمونه',
            'unit_price' => 100000,
            'quantity' => 1,
            'line_total' => 100000,
        ]);

        return $order;
    }

    public function test_paid_status_sends_sms_and_mail(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'buyer@example.com']);
        $order = $this->makeOrder($user);

        $order->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);

        Mail::assertSent(OrderPaid::class, fn (OrderPaid $mail) => $mail->order->is($order));
    }

    public function test_shipped_status_sends_notifications(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'buyer@example.com']);
        $order = $this->makeOrder($user, OrderStatus::Paid);

        $order->update(['status' => OrderStatus::Shipped]);

        Mail::assertSent(OrderShipped::class);
    }

    public function test_customer_can_view_invoice(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user, OrderStatus::Paid);

        $this->actingAs($user)
            ->get(route('orders.invoice', $order))
            ->assertOk()
            ->assertSee($order->number);

        $other = User::factory()->create();
        $this->actingAs($other)
            ->get(route('orders.invoice', $order))
            ->assertForbidden();
    }

    public function test_admin_can_view_invoice(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);
        $order = $this->makeOrder($customer, OrderStatus::Paid);

        $this->actingAs($admin)
            ->get(route('orders.invoice', $order))
            ->assertOk();
    }
}
