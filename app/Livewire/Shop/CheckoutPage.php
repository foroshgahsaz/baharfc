<?php

namespace App\Livewire\Shop;

use App\Actions\Orders\CreateOrderFromCart;
use App\Models\Address;
use App\Services\CartService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class CheckoutPage extends Component
{
    use AuthorizesRequests;

    public ?int $addressId = null;

    public string $customerNote = '';

    public function mount(CartService $cart): void
    {
        if ($cart->items()->isEmpty()) {
            $this->redirect(route('cart.index'), navigate: true);

            return;
        }

        $default = Auth::user()->defaultAddress();
        $this->addressId = $default?->id;
    }

    public function placeOrder(CreateOrderFromCart $action, CartService $cart): void
    {
        $this->validate([
            'addressId' => ['required', 'integer', 'exists:addresses,id'],
            'customerNote' => ['nullable', 'string', 'max:1000'],
        ], [
            'addressId.required' => 'آدرس تحویل را انتخاب کنید.',
        ]);

        $address = Address::query()->findOrFail($this->addressId);
        $this->authorize('update', $address);

        try {
            $order = $action->execute(Auth::user(), $address, $this->customerNote ?: null);
        } catch (InvalidArgumentException $e) {
            $this->addError('checkout', $e->getMessage());

            return;
        }

        $this->redirect(route('checkout.payment', $order), navigate: true);
    }

    public function render(CartService $cart)
    {
        return view('livewire.shop.checkout-page', [
            'items' => $cart->items(),
            'subtotal' => $cart->subtotal(),
            'discount' => $cart->discountTotal(),
            'shipping' => $cart->shippingTotal(),
            'total' => $cart->grandTotal(),
            'addresses' => Auth::user()->addresses()->latest()->get(),
            'appliedCoupon' => $cart->appliedCoupon(),
            'shippingMethod' => $cart->selectedShippingMethod(),
        ]);
    }
}
