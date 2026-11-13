<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'slug',
    'price',
    'compare_at_price',
    'image_path',
    'is_active',
    'is_featured',
    'sort_order',
])]
class Product extends Model
{
    public function formattedPrice(): string
    {
        return number_format($this->price).' تومان';
    }

    public function imageUrl(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
