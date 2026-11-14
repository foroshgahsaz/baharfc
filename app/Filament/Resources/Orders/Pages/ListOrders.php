<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Actions\Reports\ExportOrdersCsv;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('خروجی CSV سفارش‌ها')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => app(ExportOrdersCsv::class)->download()),
        ];
    }
}
