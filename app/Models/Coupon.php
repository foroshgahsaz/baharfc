<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code',
    'type',
    'value',
    'min_order_amount',
    'max_uses',
    'used_count',
    'starts_at',
    'ends_at',
    'is_active',
])]
class Coupon extends Model
{
    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isValidForSubtotal(int $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && now()->gt($this->ends_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return $subtotal >= $this->min_order_amount;
    }

    public function discountFor(int $subtotal): int
    {
        if (! $this->isValidForSubtotal($subtotal)) {
            return 0;
        }

        return match ($this->type) {
            CouponType::Percent => (int) min($subtotal, round($subtotal * ($this->value / 100))),
            CouponType::Fixed => (int) min($subtotal, $this->value),
        };
    }
}
