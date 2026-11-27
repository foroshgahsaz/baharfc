<?php

namespace App\Support;

use App\Enums\HomeSectionAnchor;
use App\Models\HomeSection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

final class HomePageCarousels
{
    /**
     * @return array<string, Collection<int, HomeSection>>
     */
    public static function groupedByAnchor(): array
    {
        if (! Schema::hasTable('home_sections')) {
            return [];
        }

        $sections = HomeSection::query()
            ->with(['category'])
            ->with(['products' => fn ($query) => $query
                ->where('products.is_active', true)
                ->orderByPivot('sort_order')
                ->orderBy('products.id')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = [];
        foreach ($sections as $section) {
            $anchor = self::anchorKey($section);
            if (! isset($grouped[$anchor])) {
                $grouped[$anchor] = collect();
            }
            $grouped[$anchor]->push($section);
        }

        return $grouped;
    }

    public static function afterAnchor(string $anchor, array $grouped): Collection
    {
        return $grouped[$anchor] ?? collect();
    }

    private static function anchorKey(HomeSection $section): string
    {
        if ($section->position_after instanceof HomeSectionAnchor) {
            return $section->position_after->value;
        }

        $raw = $section->getRawOriginal('position_after') ?? $section->getAttributes()['position_after'] ?? null;
        $parsed = HomeSectionAnchor::tryFrom((string) $raw);
        if ($parsed !== null) {
            return $parsed->value;
        }

        return HomeSectionAnchor::SpecialDeals->value;
    }
}