<?php

namespace App\Support;

use App\Models\Product;

final class ProductPagePresenter
{
    public function __construct(public Product $product) {}

    /**
     * @return list<string>
     */
    public function galleryUrls(): array
    {
        if ($this->product->images->isNotEmpty()) {
            return $this->product->images->map(fn ($img) => $img->url())->filter()->values()->all();
        }

        return $this->product->imageUrl() ? [$this->product->imageUrl()] : [];
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    public function keySpecs(): array
    {
        $specs = $this->collectSpecsFromVariants();

        if ($specs !== []) {
            return array_slice($specs, 0, 6);
        }

        $rows = [];
        if ($this->product->brand) {
            $rows[] = ['label' => 'برند', 'value' => $this->product->brand->name];
        }
        if ($this->product->category) {
            $rows[] = ['label' => 'دسته‌بندی', 'value' => $this->product->category->name];
        }
        $rows[] = ['label' => 'کد محصول', 'value' => $this->product->slug];

        return $rows;
    }

    /**
     * @return list<array{title: string, rows: list<array{label: string, value: string}>}>
     */
    public function specGroups(): array
    {
        $general = $this->collectSpecsFromVariants();
        if ($general === [] && $this->product->category) {
            $general[] = ['label' => 'دسته‌بندی', 'value' => $this->product->category->name];
        }
        if ($general === [] && $this->product->brand) {
            $general[] = ['label' => 'برند', 'value' => $this->product->brand->name];
        }

        $groups = [];
        if ($general !== []) {
            $groups[] = ['title' => 'مشخصات کلی', 'rows' => $general];
        }

        $meta = [];
        $meta[] = ['label' => 'شناسه', 'value' => $this->product->slug];
        $meta[] = ['label' => 'وضعیت', 'value' => $this->product->is_active ? 'موجود' : 'ناموجود'];

        if ($meta !== []) {
            $groups[] = ['title' => 'سایر مشخصات', 'rows' => $meta];
        }

        return $groups;
    }

    /**
     * @return list<array{label: string, value: string, color?: string}>
     */
    public function colorSwatches(): array
    {
        return $this->variantOptionValues('color', true);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    public function optionPills(): array
    {
        return $this->variantOptionValues('storage')
            ?: $this->variantOptionValues('size')
            ?: $this->variantOptionValues('volume');
    }

    public function optionGroupLabel(): string
    {
        return match ($this->primaryOptionKey()) {
            'storage' => 'حافظه داخلی',
            'size' => 'سایز',
            'volume' => 'حجم',
            default => 'گزینه',
        };
    }

    public function primaryOptionKey(): ?string
    {
        foreach (['storage', 'size', 'volume'] as $key) {
            if ($this->variantOptionValues($key) !== []) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @return list<array{id: int, options: array<string, mixed>, price: int, compare_at_price: int|null}>
     */
    public function variantsForClient(): array
    {
        return $this->product->activeVariants
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'options' => is_array($variant->options) ? $variant->options : [],
                'price' => (int) $variant->price,
                'compare_at_price' => $variant->compare_at_price !== null ? (int) $variant->compare_at_price : null,
            ])
            ->values()
            ->all();
    }

    public function hasConfigurableVariants(): bool
    {
        return $this->product->activeVariants->isNotEmpty()
            && ($this->colorSwatches() !== [] || $this->optionPills() !== []);
    }

    /**
     * @return list<array{label: string, value: string, color?: string}>
     */
    private function variantOptionValues(string $key, bool $withColor = false): array
    {
        $items = [];
        foreach ($this->product->activeVariants as $variant) {
            $options = is_array($variant->options) ? $variant->options : [];
            if (! isset($options[$key]) && ! isset($options[$this->persianOptionKey($key)])) {
                continue;
            }
            $value = (string) ($options[$key] ?? $options[$this->persianOptionKey($key)]);
            $label = $value;
            $color = null;
            if ($withColor) {
                $color = $options['color_hex'] ?? $options['hex'] ?? $this->colorHexFromName($value);
            }
            $items[$value] = ['label' => $label, 'value' => $value, 'color' => $color];
        }

        return array_values($items);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function collectSpecsFromVariants(): array
    {
        $rows = [];
        $seen = [];

        foreach ($this->product->activeVariants as $variant) {
            $options = is_array($variant->options) ? $variant->options : [];
            foreach ($options as $label => $value) {
                if (in_array($label, ['color_hex', 'hex'], true)) {
                    continue;
                }
                $key = $label.'|'.$value;
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                $rows[] = [
                    'label' => is_string($label) ? $this->humanOptionLabel($label) : 'مشخصه',
                    'value' => (string) $value,
                ];
            }
        }

        return $rows;
    }

    private function persianOptionKey(string $key): string
    {
        return match ($key) {
            'color' => 'رنگ',
            'storage' => 'حافظه',
            'size' => 'سایز',
            default => $key,
        };
    }

    private function humanOptionLabel(string $key): string
    {
        return match ($key) {
            'color', 'رنگ' => 'رنگ',
            'storage', 'حافظه' => 'حافظه داخلی',
            'size', 'سایز' => 'سایز',
            'volume' => 'حجم',
            'ram' => 'مقدار رم',
            default => $key,
        };
    }

    private function colorHexFromName(string $name): ?string
    {
        return match (mb_strtolower(trim($name))) {
            'آبی', 'ابی' => '#3b82f6',
            'مشکی', 'سیاه' => '#1f2937',
            'سفید' => '#f8fafc',
            'قرمز' => '#ef4444',
            'سبز' => '#22c55e',
            'طلایی', 'زرد' => '#eab308',
            'نقره‌ای', 'نقره ای' => '#94a3b8',
            default => '#cbd5e1',
        };
    }
}
