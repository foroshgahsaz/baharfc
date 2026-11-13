<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'slug',
    'category_id',
    'brand_id',
    'short_description',
    'description',
    'meta_title',
    'meta_description',
    'price',
    'compare_at_price',
    'image_path',
    'is_active',
    'is_featured',
    'sort_order',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function formattedPrice(): string
    {
        return number_format($this->price).' تومان';
    }

    public function hasDiscount(): bool
    {
        return $this->compare_at_price !== null && $this->compare_at_price > $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round((1 - ($this->price / $this->compare_at_price)) * 100);
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
