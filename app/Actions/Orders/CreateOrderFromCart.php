<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateOrderFromCart
{
    public function __construct(
        protected CartService $cart,
    ) {}

    public function execute(User $user, Address $address, ?string $customerNote = null): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new InvalidArgumentException('سبد خرید خالی است.');
        }

        if ($address->user_id !== $user->id) {
            throw new InvalidArgumentException('آدرس انتخاب‌شده معتبر نیست.');
        }

        $shipping = $this->cart->selectedShippingMethod();
        $coupon = $this->cart->appliedCoupon();
        $subtotal = $this->cart->subtotal();
        $discount = $this->cart->discountTotal();
        $shippingTotal = $this->cart->shippingTotal();
        $total = $this->cart->grandTotal();

        return DB::transaction(function () use ($user, $address, $customerNote, $items, $shipping, $coupon, $subtotal, $discount, $shippingTotal, $total) {
            $order = Order::query()->create([
                'number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => OrderStatus::PendingPayment,
                'subtotal' => $subtotal,
                'discount_total' => $discount,
                'shipping_total' => $shippingTotal,
                'total' => $total,
                'coupon_code' => $coupon?->code,
                'shipping_method' => $shipping?->name,
                'recipient_name' => $address->recipient_name,
                'recipient_phone' => $address->phone,
                'province' => $address->province,
                'city' => $address->city,
                'address_line' => $address->address_line,
                'postal_code' => $address->postal_code,
                'customer_note' => $customerNote,
            ]);

            foreach ($items as $cartItem) {
                $product = $cartItem->product;
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_slug' => $product->slug,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'line_total' => $cartItem->lineTotal(),
                ]);
            }

            if ($coupon) {
                Coupon::query()->whereKey($coupon->id)->increment('used_count');
            }

            $this->cart->clear();

            return $order->load('items');
        });
    }

    protected function generateOrderNumber(): string
    {
        do {
            $number = 'BHR-'.now()->format('ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
