<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;

    public int $quantity = 1;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
    }

    public function add(CartService $cart): void
    {
        $product = Product::query()->where('is_active', true)->findOrFail($this->productId);
        $cart->add($product, $this->quantity);
        $this->dispatch('cart-updated');
        session()->flash('cart_toast', 'به سبد خرید افزوده شد.');
    }

    public function render()
    {
        return view('livewire.shop.add-to-cart');
    }
}
