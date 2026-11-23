<?php

namespace App\Enums;

enum HomeSectionAnchor: string
{
    case Start = 'start';
    case Hero = 'hero';
    case CategoryStrip = 'category_strip';
    case SpecialDeals = 'special_deals';
    case Featured = 'featured';
    case Latest = 'latest';
    case Newsletter = 'newsletter';

    public function label(): string
    {
        return match ($this) {
            self::Start => 'ابتدای صفحه (قبل از اسلایدر)',
            self::Hero => 'بعد از اسلایدر اصلی',
            self::CategoryStrip => 'بعد از دسته‌بندی محصولات',
            self::SpecialDeals => 'بعد از پیشنهاد شگفت‌انگیز',
            self::Featured => 'بعد از محصولات ویژه',
            self::Latest => 'بعد از جدیدترین‌ها',
            self::Newsletter => 'بعد از خبرنامه',
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