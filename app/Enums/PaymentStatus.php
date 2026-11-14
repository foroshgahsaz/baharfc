<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Redirected = 'redirected';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار',
            self::Redirected => 'انتقال به درگاه',
            self::Paid => 'موفق',
            self::Failed => 'ناموفق',
            self::Cancelled => 'لغو شده',
        };
    }
}
