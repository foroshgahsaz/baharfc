<?php

namespace App\Support;

use Carbon\CarbonInterface;
use DateTimeInterface;
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
                $value = \Illuminate\Support\Carbon::parse($value);
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
                ? \Illuminate\Support\Carbon::instance($value)
                : \Illuminate\Support\Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }

        $jalali = Jalalian::fromDateTime($carbon);

        return $jalali->ago();
    }
}