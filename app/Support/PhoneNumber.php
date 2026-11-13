<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(string $input): ?string
    {
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        if (str_starts_with($digits, '98') && strlen($digits) === 12) {
            $digits = '0'.substr($digits, 2);
        }

        if (str_starts_with($digits, '9') && strlen($digits) === 10) {
            $digits = '0'.$digits;
        }

        if (! preg_match('/^09\d{9}$/', $digits)) {
            return null;
        }

        return $digits;
    }
}
