<?php

namespace App\Support;

class IranLocations
{
    /**
     * @return array<string, list<string>>
     */
    public static function provinces(): array
    {
        return config('iran_locations.provinces', []);
    }

    /**
     * @return list<string>
     */
    public static function provinceNames(): array
    {
        return array_keys(self::provinces());
    }

    /**
     * @return list<string>
     */
    public static function citiesForProvince(?string $province): array
    {
        if (blank($province)) {
            return [];
        }

        return self::provinces()[$province] ?? [];
    }
}
