<?php

namespace App\Services;

use App\Contracts\SmsSender;
use App\Enums\OrderStatus;
use App\Mail\OrderPaid;
use App\Mail\OrderShipped;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class OrderStatusNotificationService
{
    public function __construct(
        private readonly SmsSender $sms,
    ) {}

    public function notifyIfNeeded(Order $order, ?OrderStatus $previous): void
    {
        if ($previous === $order->status) {
            return;
        }

        match ($order->status) {
            OrderStatus::Paid => $this->notifyPaid($order),
            OrderStatus::Shipped => $this->notifyShipped($order),
            default => null,
        };
    }

    public function notifyPaid(Order $order): void
    {
        $this->sms->send(
            $order->recipient_phone,
            "فروشگاه بهار: سفارش {$order->number} با موفقیت پرداخت شد. مبلغ ".number_format($order->total).' تومان.',
        );

        $email = $order->user?->email;
        if (filled($email)) {
            Mail::to($email)->send(new OrderPaid($order));
        }
    }

    public function notifyShipped(Order $order): void
    {
        $this->sms->send(
            $order->recipient_phone,
            "فروشگاه بهار: سفارش {$order->number} ارسال شد. از خرید شما سپاسگزاریم.",
        );

        $email = $order->user?->email;
        if (filled($email)) {
            Mail::to($email)->send(new OrderShipped($order));
        }
    }
}
