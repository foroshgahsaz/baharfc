<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $open = false;

    public ?int $highlightProductId = null;

    #[On('cart-updated')]
    public function onCartUpdated(?int $productId = null): void
    {
        $this->highlightProductId = $productId;
        $this->open = true;
        $this->dispatch('cart-header-refresh');
    }

    public function close(): void
    {
        $this->open = false;
        $this->highlightProductId = null;
    }

    public function render(CartService $cart)
    {
        $items = $cart->items();
        $highlight = null;
        if ($this->highlightProductId) {
            $highlight = Product::query()->find($this->highlightProductId);
        }

        return view('livewire.shop.cart-drawer', [
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'total' => $cart->grandTotal(),
            'highlight' => $highlight,
            'itemCount' => $cart->itemCount(),
        ]);
    }
}