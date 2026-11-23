<?php

namespace App\Support;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

class JalaliDate
{
    public static function format(null|DateTimeInterface|string $value, string $pattern = 'Y/m/d H:i'): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! $value instanceof DateTimeInterface) {
            try {
                $value = Carbon::parse($value);
            } catch (\Throwable) {
                return null;
            }
        }

        if ($value instanceof CarbonInterface && $value->year < 1970) {
            return null;
        }

        return Jalalian::fromDateTime($value)->format($pattern);
    }

    public static function date(null|DateTimeInterface|string $value): ?string
    {
        return self::format($value, 'Y/m/d');
    }

    public static function dateTime(null|DateTimeInterface|string $value): ?string
    {
        return self::format($value, 'Y/m/d H:i');
    }

    public static function human(null|DateTimeInterface|string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            $carbon = $value instanceof DateTimeInterface
                ? Carbon::instance($value)
                : Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }

        $jalali = Jalalian::fromDateTime($carbon);

        return $jalali->ago();
    }

    public static function fromFormString(?string $value, bool $withTime = true): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $normalized = self::toEnglishDigits(trim($value));

        $formats = $withTime
            ? ['Y/m/d H:i', 'Y-m-d H:i', 'Y/m/d H:i:s', 'Y/m/d']
            : ['Y/m/d', 'Y-m-d'];

        foreach ($formats as $format) {
            try {
                $jalali = Jalalian::fromFormat($format, $normalized);

                return Carbon::instance($jalali->toCarbon());
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    public static function toEnglishDigits(string $value): string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        return str_replace($persian, range(0, 9), str_replace($arabic, range(0, 9), $value));
    }

    public static function toPersianDigits(int|string $value): string
    {
        return strtr((string) $value, [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',
        ]);
    }
}
