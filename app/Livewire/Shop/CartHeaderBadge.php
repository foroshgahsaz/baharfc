<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartHeaderBadge extends Component
{
    #[On('cart-header-refresh')]
    public function refreshCart(): void
    {
        // triggers re-render
    }

    public function render(CartService $cart)
    {
        return view('livewire.shop.cart-header-badge', [
            'cartItemCount' => $cart->itemCount(),
            'cartSubtotal' => $cart->subtotal(),
        ]);
    }
}