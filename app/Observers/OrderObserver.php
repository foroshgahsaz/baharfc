<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderStatusNotificationService;

class OrderObserver
{
    public function __construct(
        private readonly OrderStatusNotificationService $notifications,
    ) {}

    public function updated(Order $order): void
    {
        if (! $order->wasChanged('status')) {
            return;
        }

        $previousValue = $order->getOriginal('status');
        $previous = $previousValue instanceof OrderStatus
            ? $previousValue
            : ($previousValue ? OrderStatus::from($previousValue) : null);

        $this->notifications->notifyIfNeeded($order, $previous);
    }
}
