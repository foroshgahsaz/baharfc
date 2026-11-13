<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'session_id',
    'product_id',
    'product_variant_id',
    'quantity',
])]
class CartItem extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function unitPrice(): int
    {
        if ($this->product_variant_id && $this->variant) {
            return (int) $this->variant->price;
        }

        return (int) $this->product->sellablePrice();
    }

    public function lineTotal(): int
    {
        return $this->unitPrice() * $this->quantity;
    }

    public function displayName(): string
    {
        $name = $this->product->name;
        if ($this->variant) {
            $name .= ' — '.$this->variant->name;
        }

        return $name;
    }
}
