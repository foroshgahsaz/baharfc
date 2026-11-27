<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class SeederMedia
{
    private const FASHION_DIR = 'seeders/media/fashion';

    /**
     * @var array<string, string>
     */
    private static array $imagePool = [
        'women-coat',
        'women-jeans',
        'women-dress',
        'men-shirt',
        'men-jacket',
        'men-sneakers',
        'bag-leather',
        'tshirt',
        'sport-set',
        'knit',
        'accessory',
        'banner-fashion',
    ];

    public static function publishFashionImage(string $imageKey, string $storagePath): ?string
    {
        $basename = self::resolveImageKey($imageKey);
        $source = database_path(self::FASHION_DIR.'/'.$basename.'.jpg');

        if (! File::isFile($source)) {
            return null;
        }

        $disk = Storage::disk('public');
        $directory = trim(dirname($storagePath), '.');
        if ($directory !== '' && $directory !== '/') {
            $disk->makeDirectory($directory);
        }

        $disk->put($storagePath, File::get($source));

        return $storagePath;
    }

    public static function productImagePath(string $slug, string $imageKey): string
    {
        return 'products/seed/'.$slug.'-'.self::resolveImageKey($imageKey).'.jpg';
    }

    public static function bannerImagePath(string $slug): string
    {
        return 'banners/seed/'.$slug.'.jpg';
    }

    private static function resolveImageKey(string $imageKey): string
    {
        if (in_array($imageKey, self::$imagePool, true)) {
            return $imageKey;
        }

        $index = abs(crc32($imageKey)) % count(self::$imagePool);

        return self::$imagePool[$index];
    }
}