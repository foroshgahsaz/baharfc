<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use App\Models\ShippingMethod;
use App\Services\CartService;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class CartPage extends Component
{
    public string $couponCode = '';

    public ?int $shippingMethodId = null;

    public function mount(CartService $cart): void
    {
        $coupon = $cart->appliedCoupon();
        $this->couponCode = $coupon?->code ?? '';
        $this->shippingMethodId = $cart->selectedShippingMethod()?->id;
    }

    public function updateQuantity(int $cartItemId, int $quantity, CartService $cart): void
    {
        $item = CartItem::query()->findOrFail($cartItemId);

        try {
            $cart->updateQuantity($item, $quantity);
        } catch (InvalidArgumentException $e) {
            $this->addError('cart', $e->getMessage());
        }
    }

    public function removeItem(int $cartItemId, CartService $cart): void
    {
        $item = CartItem::query()->findOrFail($cartItemId);

        try {
            $cart->remove($item);
        } catch (InvalidArgumentException $e) {
            $this->addError('cart', $e->getMessage());
        }
    }

    public function applyCoupon(CartService $cart): void
    {
        try {
            $cart->applyCoupon($this->couponCode);
            Session::flash('cart_status', 'کد تخفیف اعمال شد.');
        } catch (InvalidArgumentException $e) {
            $this->addError('couponCode', $e->getMessage());
        }
    }

    public function removeCoupon(CartService $cart): void
    {
        $cart->removeCoupon();
        $this->couponCode = '';
        Session::flash('cart_status', 'کد تخفیف حذف شد.');
    }

    public function updatedShippingMethodId(?int $value, CartService $cart): void
    {
        if ($value) {
            $cart->selectShippingMethod($value);
        }
    }

    public function render(CartService $cart)
    {
        return view('livewire.shop.cart-page', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'discount' => $cart->discountTotal(),
            'shipping' => $cart->shippingTotal(),
            'total' => $cart->grandTotal(),
            'appliedCoupon' => $cart->appliedCoupon(),
            'shippingMethods' => ShippingMethod::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
