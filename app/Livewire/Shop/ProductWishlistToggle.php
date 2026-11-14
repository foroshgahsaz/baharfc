<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductWishlistToggle extends Component
{
    public int $productId;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
    }

    public function toggle(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');

            return;
        }

        $user = Auth::user();
        $existing = WishlistItem::query()
            ->where('user_id', $user->id)
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            WishlistItem::query()->create([
                'user_id' => $user->id,
                'product_id' => $this->productId,
            ]);
        }
    }

    public function isWishlisted(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return WishlistItem::query()
            ->where('user_id', Auth::id())
            ->where('product_id', $this->productId)
            ->exists();
    }

    public function render()
    {
        Product::query()->findOrFail($this->productId);

        return view('livewire.shop.product-wishlist-toggle', [
            'wishlisted' => $this->isWishlisted(),
        ]);
    }
}
