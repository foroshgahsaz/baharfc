<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Attributes\On;
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

    public function add(CartService $cart, ?int $selectedVariantId = null): void
    {
        if ($selectedVariantId !== null) {
            $this->productVariantId = $selectedVariantId;
        }

        $product = Product::query()->where('is_active', true)->findOrFail($this->productId);

        if ($product->isVariable() && $this->productVariantId === null) {
            $product->loadMissing('activeVariants');
            if ($product->activeVariants->isNotEmpty()) {
                $this->productVariantId = $product->activeVariants->first()->id;
            }
        }

        try {
            $cart->add($product, $this->quantity, $this->productVariantId);
        } catch (\InvalidArgumentException $e) {
            $this->addError('variant', $e->getMessage());

            return;
        }

        $this->dispatch(
            'cart-updated',
            productId: $this->productId,
            productVariantId: $this->productVariantId,
        );
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

    #[On('product-variant-selected')]
    public function applyVariant(mixed $variantId = null): void
    {
        if (is_array($variantId)) {
            $variantId = $variantId['variantId'] ?? null;
        }

        if ($variantId !== null) {
            $this->productVariantId = (int) $variantId;
        }
    }

    public function render()
    {
        $product = Product::query()->with('activeVariants')->findOrFail($this->productId);

        return view('livewire.shop.add-to-cart', ['product' => $product]);
    }
}
