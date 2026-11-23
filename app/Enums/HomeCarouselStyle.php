<?php

namespace App\Enums;

enum HomeCarouselStyle: string
{
    case Style1 = 'style_1';
    case Style2 = 'style_2';

    public function label(): string
    {
        return match ($this) {
            self::Style1 => 'استایل ۱ (کارت سبز / مبلمان)',
            self::Style2 => 'استایل ۲ (برند / کاروسل بنفش)',
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