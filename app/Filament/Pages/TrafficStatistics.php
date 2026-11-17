<?php

namespace App\Filament\Pages;

use App\Actions\Reports\ExportTrafficStatsCsv;
use App\Enums\TrafficEntityType;
use App\Filament\Concerns\ChecksAdminPageAccess;
use App\Support\SchemaGuard;
use App\Services\Traffic\TrafficStatsService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TrafficStatistics extends Page
{
    use ChecksAdminPageAccess;

    protected static function adminPagePermission(): string
    {
        return 'analytics.view';
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'آمار بازدید';

    protected static ?string $title = 'آمار بازدید داخلی';

    protected static string|UnitEnum|null $navigationGroup = 'گزارش‌ها';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        if (! SchemaGuard::hasTable('traffic_daily_stats')) {
            return false;
        }

        return auth()->user()?->canAdmin('analytics.view') ?? false;
    }

    protected static ?string $slug = 'traffic-statistics';

    protected string $view = 'filament.pages.traffic-statistics';

    public ?string $fromDate = null;

    public ?string $toDate = null;

    public function mount(TrafficStatsService $stats): void
    {
        $this->fromDate = now()->subDays(6)->toDateString();
        $this->toDate = now()->toDateString();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('خروجی CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (ExportTrafficStatsCsv $exporter): mixed {
                    [$from, $to] = $this->parsedRange();

                    return $exporter->download($from, $to);
                }),
        ];
    }

    public function applyRange(): void
    {
        try {
            $this->parsedRange();
            Notification::make()->title('بازه به‌روز شد')->success()->send();
        } catch (\InvalidArgumentException $e) {
            Notification::make()->title($e->getMessage())->danger()->send();
        }
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function parsedRange(): array
    {
        $from = Carbon::parse($this->fromDate ?? now()->subDays(6))->startOfDay();
        $to = Carbon::parse($this->toDate ?? now())->endOfDay();

        if ($from->gt($to)) {
            throw new \InvalidArgumentException('تاریخ شروع نمی‌تواند بعد از پایان باشد.');
        }

        return [$from, $to];
    }

    public function getViewData(): array
    {
        try {
            [$from, $to] = $this->parsedRange();
        } catch (\InvalidArgumentException) {
            $from = now()->subDays(6)->startOfDay();
            $to = now()->endOfDay();
        }

        $service = app(TrafficStatsService::class);

        return [
            'fromLabel' => $from->format('Y/m/d'),
            'toLabel' => $to->format('Y/m/d'),
            'totalViews' => $service->totalViewsBetween($from, $to),
            'productViews' => $service->totalViewsBetween($from, $to, TrafficEntityType::Product),
            'blogViews' => $service->totalViewsBetween($from, $to, TrafficEntityType::BlogPost),
            'pageViews' => $service->totalViewsBetween($from, $to, TrafficEntityType::Page)
                + $service->totalViewsBetween($from, $to, TrafficEntityType::Category),
            'topProducts' => $service->topByType(TrafficEntityType::Product, $from, $to),
            'topBlogPosts' => $service->topByType(TrafficEntityType::BlogPost, $from, $to),
            'topPages' => $service->topByType(TrafficEntityType::Page, $from, $to),
            'topCategories' => $service->topByType(TrafficEntityType::Category, $from, $to),
        ];
    }
}