<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class CartService
{
    public const COUPON_SESSION_KEY = 'cart.coupon_code';

    public const SHIPPING_SESSION_KEY = 'cart.shipping_method_id';

    public function sessionId(): string
    {
        return Session::getId();
    }

    public function items(): Collection
    {
        $query = CartItem::query()->with(['product' => fn ($q) => $q->where('is_active', true)]);

        if ($userId = Auth::id()) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $this->sessionId())->whereNull('user_id');
        }

        return $query->get()->filter(fn (CartItem $item) => $item->product !== null);
    }

    public function add(Product $product, int $quantity = 1): CartItem
    {
        if (! $product->is_active) {
            throw new InvalidArgumentException('محصول غیرفعال است.');
        }

        $quantity = max(1, min(99, $quantity));

        $attributes = [
            'product_id' => $product->id,
        ];

        if ($userId = Auth::id()) {
            $attributes['user_id'] = $userId;
        } else {
            $attributes['session_id'] = $this->sessionId();
        }

        $item = CartItem::query()->firstOrNew($attributes);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
        $item->save();

        return $item->load('product');
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        $this->assertOwnsItem($item);

        if ($quantity <= 0) {
            $item->delete();

            return;
        }

        $item->update(['quantity' => min(99, $quantity)]);
    }

    public function remove(CartItem $item): void
    {
        $this->assertOwnsItem($item);
        $item->delete();
    }

    public function clear(): void
    {
        if ($userId = Auth::id()) {
            CartItem::query()->where('user_id', $userId)->delete();
        } else {
            CartItem::query()
                ->where('session_id', $this->sessionId())
                ->whereNull('user_id')
                ->delete();
        }

        Session::forget(self::COUPON_SESSION_KEY);
    }

    public function itemCount(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function subtotal(): int
    {
        return $this->items()->sum(fn (CartItem $item) => $item->lineTotal());
    }

    public function appliedCoupon(): ?Coupon
    {
        $code = Session::get(self::COUPON_SESSION_KEY);

        if (blank($code)) {
            return null;
        }

        return Coupon::query()->where('code', $code)->first();
    }

    public function applyCoupon(string $code): Coupon
    {
        $coupon = Coupon::query()->where('code', strtoupper(trim($code)))->first();

        if ($coupon === null || ! $coupon->isValidForSubtotal($this->subtotal())) {
            throw new InvalidArgumentException('کد تخفیف معتبر نیست.');
        }

        Session::put(self::COUPON_SESSION_KEY, $coupon->code);

        return $coupon;
    }

    public function removeCoupon(): void
    {
        Session::forget(self::COUPON_SESSION_KEY);
    }

    public function discountTotal(): int
    {
        $coupon = $this->appliedCoupon();

        return $coupon ? $coupon->discountFor($this->subtotal()) : 0;
    }

    public function selectedShippingMethod(): ?ShippingMethod
    {
        $id = Session::get(self::SHIPPING_SESSION_KEY);

        if ($id) {
            return ShippingMethod::query()->where('is_active', true)->find($id);
        }

        return ShippingMethod::query()->where('is_active', true)->orderBy('sort_order')->first();
    }

    public function selectShippingMethod(int $shippingMethodId): void
    {
        $method = ShippingMethod::query()->where('is_active', true)->findOrFail($shippingMethodId);
        Session::put(self::SHIPPING_SESSION_KEY, $method->id);
    }

    public function shippingTotal(): int
    {
        return $this->selectedShippingMethod()?->price ?? 0;
    }

    public function grandTotal(): int
    {
        return max(0, $this->subtotal() - $this->discountTotal() + $this->shippingTotal());
    }

    public function mergeGuestCartIntoUser(User $user): void
    {
        $sessionId = $this->sessionId();

        DB::transaction(function () use ($user, $sessionId): void {
            $guestItems = CartItem::query()
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->get();

            foreach ($guestItems as $guestItem) {
                $existing = CartItem::query()
                    ->where('user_id', $user->id)
                    ->where('product_id', $guestItem->product_id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'quantity' => min(99, $existing->quantity + $guestItem->quantity),
                    ]);
                    $guestItem->delete();
                } else {
                    $guestItem->update([
                        'user_id' => $user->id,
                        'session_id' => null,
                    ]);
                }
            }
        });
    }

    protected function assertOwnsItem(CartItem $item): void
    {
        if (Auth::id() && $item->user_id === Auth::id()) {
            return;
        }

        if (! Auth::id() && $item->session_id === $this->sessionId() && $item->user_id === null) {
            return;
        }

        throw new InvalidArgumentException('دسترسی به این آیتم سبد مجاز نیست.');
    }
}
