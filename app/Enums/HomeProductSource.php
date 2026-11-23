<?php

namespace App\Enums;

enum HomeProductSource: string
{
    case Manual = 'manual';
    case Category = 'category';

    public function label(): string
    {
        return match ($this) {
            self::Manual => 'انتخاب دستی محصولات',
            self::Category => 'از دسته‌بندی',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}