<?php

namespace App\Models;

use App\Enums\HomeCarouselStyle;
use App\Enums\HomeProductSource;
use App\Enums\HomeSectionAnchor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class HomeSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'style',
        'source',
        'category_id',
        'product_limit',
        'more_label',
        'more_url',
        'position_after',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'style' => HomeCarouselStyle::class,
            'source' => HomeProductSource::class,
            'position_after' => HomeSectionAnchor::class,
            'product_limit' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'home_section_product')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function resolvedProducts(): Collection
    {
        if ($this->source === HomeProductSource::Manual) {
            return $this->products()
                ->where('products.is_active', true)
                ->get();
        }

        if ($this->category_id === null) {
            return collect();
        }

        return Product::query()
            ->where('is_active', true)
            ->where('category_id', $this->category_id)
            ->orderBy('sort_order')
            ->latest()
            ->limit(max(1, min(24, (int) $this->product_limit)))
            ->get();
    }

    public function moreLinkUrl(): ?string
    {
        if (filled($this->more_url)) {
            return $this->more_url;
        }

        if ($this->source === HomeProductSource::Category && $this->category) {
            return route('products.index', ['category' => $this->category->slug]);
        }

        return route('products.index');
    }

    public function moreLinkLabel(): string
    {
        return filled($this->more_label) ? $this->more_label : 'محصولات بیشتر';
    }
}