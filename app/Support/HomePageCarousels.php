<?php

namespace App\Support;

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
            ->with(['category', 'products'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = [];
        foreach ($sections as $section) {
            $anchor = $section->position_after?->value ?? 'special_deals';
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
}