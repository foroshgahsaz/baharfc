<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;

    public int $quantity = 1;

    public ?int $productVariantId = null;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        $product = Product::query()->with('activeVariants')->findOrFail($productId);
        if ($product->isVariable() && $product->activeVariants->isNotEmpty()) {
            $this->productVariantId = $product->activeVariants->first()->id;
        }
    }

    public function add(CartService $cart): void
    {
        $product = Product::query()->where('is_active', true)->findOrFail($this->productId);

        try {
            $cart->add($product, $this->quantity, $this->productVariantId);
        } catch (\InvalidArgumentException $e) {
            $this->addError('variant', $e->getMessage());

            return;
        }

        $this->dispatch('cart-updated');
        session()->flash('cart_toast', 'به سبد خرید افزوده شد.');
    }

    public function render()
    {
        $product = Product::query()->with('activeVariants')->findOrFail($this->productId);

        return view('livewire.shop.add-to-cart', ['product' => $product]);
    }
}
