<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class FrameworkWritablePaths
{
    /**
     * @return list<string>
     */
    public static function relativeDirectories(): array
    {
        return [
            'bootstrap/cache',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ];
    }

    /**
     * @return array<string, bool> path => writable
     */
    public static function ensurePresent(): array
    {
        $base = base_path();
        $status = [];

        foreach (self::relativeDirectories() as $relative) {
            $path = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);

            try {
                if (! File::isDirectory($path)) {
                    File::makeDirectory($path, 0775, true);
                }

                $status[$relative] = is_writable($path);
            } catch (\Throwable) {
                $status[$relative] = false;
            }
        }

        return $status;
    }

    public static function isHealthy(): bool
    {
        foreach (self::ensurePresent() as $writable) {
            if (! $writable) {
                return false;
            }
        }

        return true;
    }

    public static function repairShellHint(string $webUser = 'www-data'): string
    {
        return implode(' ', [
            'mkdir -p bootstrap/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            '&&',
            "chown -R {$webUser}:{$webUser}",
            'bootstrap/cache storage',
            '&&',
            'chmod -R ug+rwx bootstrap/cache storage',
        ]);
    }
}