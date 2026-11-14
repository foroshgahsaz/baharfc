<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'slug',
    'price',
    'is_active',
    'sort_order',
])]
class ShippingMethod extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function formattedPrice(): string
    {
        return number_format($this->price).' تومان';
    }
}
