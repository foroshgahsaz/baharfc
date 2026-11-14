<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'در انتظار پرداخت',
            self::Paid => 'پرداخت شده',
            self::Processing => 'در حال پردازش',
            self::Shipped => 'ارسال شده',
            self::Delivered => 'تحویل شده',
            self::Cancelled => 'لغو شده',
            self::Failed => 'ناموفق',
        };
    }
}
