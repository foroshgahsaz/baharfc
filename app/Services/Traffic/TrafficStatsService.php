<?php

namespace App\Services\Traffic;

use App\Enums\TrafficEntityType;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\TrafficDailyStat;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TrafficStatsService
{
    public function record(TrafficEntityType $type, string $key): void
    {
        $key = mb_substr(trim($key), 0, 191);
        if ($key === '') {
            return;
        }

        $today = now()->toDateString();

        $query = TrafficDailyStat::query()
            ->whereDate('stat_date', $today)
            ->where('entity_type', $type->value)
            ->where('entity_key', $key);

        $updated = (clone $query)->increment('views');
        if ($updated === 0) {
            try {
                TrafficDailyStat::query()->create([
                    'stat_date' => $today,
                    'entity_type' => $type->value,
                    'entity_key' => $key,
                    'views' => 1,
                ]);
            } catch (\Illuminate\Database\QueryException) {
                (clone $query)->increment('views');
            }
        }
    }

    /**
     * @return Collection<int, object{entity_key: string, total_views: int, label: string}>
     */
    public function topByType(
        TrafficEntityType $type,
        CarbonInterface $from,
        CarbonInterface $to,
        int $limit = 15,
    ): Collection {
        $rows = TrafficDailyStat::query()
            ->selectRaw('entity_key, SUM(views) as total_views')
            ->where('entity_type', $type)
            ->whereBetween('stat_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('entity_key')
            ->orderByDesc('total_views')
            ->limit($limit)
            ->get();

        return $rows->map(function ($row) use ($type) {
            $row->label = $this->labelFor($type, (string) $row->entity_key);

            return $row;
        });
    }

    public function totalViewsBetween(CarbonInterface $from, CarbonInterface $to, ?TrafficEntityType $type = null): int
    {
        $query = TrafficDailyStat::query()
            ->whereBetween('stat_date', [$from->toDateString(), $to->toDateString()]);

        if ($type !== null) {
            $query->where('entity_type', $type);
        }

        return (int) $query->sum('views');
    }

    public function pageLabel(string $key): string
    {
        return match ($key) {
            'home' => 'صفحه اصلی',
            'blog_index' => 'فهرست بلاگ',
            'products_index' => 'فهرست محصولات',
            default => $key,
        };
    }

    public function labelFor(TrafficEntityType $type, string $key): string
    {
        return match ($type) {
            TrafficEntityType::Product => Product::query()->where('slug', $key)->value('name') ?? $key,
            TrafficEntityType::BlogPost => BlogPost::query()->where('slug', $key)->value('title') ?? $key,
            TrafficEntityType::Category => Category::query()->where('slug', $key)->value('name') ?? $key,
            TrafficEntityType::Page => $this->pageLabel($key),
        };
    }
}