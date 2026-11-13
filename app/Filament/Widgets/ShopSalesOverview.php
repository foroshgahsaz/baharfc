<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopSalesOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $paidStatuses = [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered];

        $salesTotal = Order::query()->whereIn('status', $paidStatuses)->sum('total');
        $ordersToday = Order::query()->whereDate('created_at', today())->count();
        $pendingPayment = Order::query()->where('status', OrderStatus::PendingPayment)->count();
        $paidCount = Order::query()->whereIn('status', $paidStatuses)->count();

        return [
            Stat::make('فروش (تومان)', number_format($salesTotal))
                ->description('مجموع سفارش‌های پرداخت‌شده')
                ->descriptionIcon('heroicon-m-banknotes'),
            Stat::make('سفارش‌های امروز', (string) $ordersToday)
                ->description('ثبت‌شده در ۲۴ ساعت اخیر')
                ->descriptionIcon('heroicon-m-shopping-bag'),
            Stat::make('پرداخت‌شده', (string) $paidCount)
                ->description('در انتظار پرداخت: '.$pendingPayment)
                ->descriptionIcon('heroicon-m-check-badge'),
        ];
    }
}
