<?php

namespace App\Livewire\Shop;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $open = false;

    public ?int $highlightProductId = null;

    public ?int $highlightVariantId = null;

    #[On('cart-updated')]
    public function onCartUpdated(?int $productId = null, ?int $productVariantId = null): void
    {
        $this->highlightProductId = $productId;
        $this->highlightVariantId = $productVariantId;
        $this->open = true;
        $this->dispatch('cart-header-refresh');
    }

    public function close(): void
    {
        $this->open = false;
        $this->highlightProductId = null;
        $this->highlightVariantId = null;
    }

    public function render(CartService $cart)
    {
        $items = $cart->items();
        $highlightItem = null;
        if ($this->highlightProductId) {
            $highlightItem = $items->first(function ($item) {
                if ($item->product_id !== $this->highlightProductId) {
                    return false;
                }

                if ($this->highlightVariantId !== null) {
                    return (int) $item->product_variant_id === (int) $this->highlightVariantId;
                }

                return true;
            });
        }

        return view('livewire.shop.cart-drawer', [
            'items' => $items,
            'subtotal' => $cart->subtotal(),
            'total' => $cart->grandTotal(),
            'highlightItem' => $highlightItem,
            'itemCount' => $cart->itemCount(),
        ]);
    }
}