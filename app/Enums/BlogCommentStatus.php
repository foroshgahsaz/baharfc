<?php

namespace App\Enums;

enum BlogCommentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Spam = 'spam';
    case Trash = 'trash';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار تأیید',
            self::Approved => 'تأیید شده',
            self::Spam => 'اسپم',
            self::Trash => 'سطل زباله',
        };
    }
}