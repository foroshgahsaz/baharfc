<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\ProductVariant;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductPriceBox extends Component
{
    public int $productId;

    public ?int $productVariantId = null;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        $product = Product::query()->with('activeVariants')->findOrFail($productId);
        if ($product->isVariable() && $product->activeVariants->isNotEmpty()) {
            $this->productVariantId = $product->activeVariants->first()->id;
        }
    }

    #[On('product-variant-selected')]
    public function applyVariant(?int $variantId = null): void
    {
        $this->productVariantId = $variantId;
    }

    public function render()
    {
        $product = Product::query()->with('activeVariants')->findOrFail($this->productId);
        $variant = $this->resolvedVariant($product);

        $price = (int) ($variant?->price ?? $product->price);
        $compareAt = $variant?->compare_at_price ?? $product->compare_at_price;
        $hasDiscount = $compareAt !== null && $compareAt > $price;
        $discountPercent = $hasDiscount ? (int) round((1 - ($price / $compareAt)) * 100) : null;

        return view('livewire.shop.product-price-box', [
            'product' => $product,
            'price' => $price,
            'compareAt' => $compareAt,
            'hasDiscount' => $hasDiscount,
            'discountPercent' => $discountPercent,
        ]);
    }

    private function resolvedVariant(Product $product): ?ProductVariant
    {
        if ($this->productVariantId === null) {
            return null;
        }

        return $product->activeVariants->firstWhere('id', $this->productVariantId);
    }
}
