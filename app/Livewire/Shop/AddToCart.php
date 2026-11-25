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

    public string $ui = 'default';

    public function mount(int $productId, string $ui = 'default'): void
    {
        $this->ui = $ui;
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

        $this->dispatch('cart-updated', productId: $this->productId);
        $this->dispatch('cart-header-refresh');
    }

    public function incrementQuantity(): void
    {
        $this->quantity = min(99, $this->quantity + 1);
    }

    public function decrementQuantity(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function render()
    {
        $product = Product::query()->with('activeVariants')->findOrFail($this->productId);

        return view('livewire.shop.add-to-cart', ['product' => $product]);
    }
}
