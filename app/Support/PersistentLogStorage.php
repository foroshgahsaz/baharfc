<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class PersistentLogStorage
{
    public static function logsRoot(): string
    {
        $configured = (string) env('APP_LOG_ROOT', '');

        if ($configured !== '') {
            return rtrim($configured, '/\\');
        }

        return storage_path('logs');
    }

    public static function laravelLogPath(): string
    {
        return self::logsRoot().'/laravel.log';
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public static function ensureLogsDirectory(): array
    {
        $root = self::logsRoot();

        try {
            if (! File::isDirectory($root)) {
                File::makeDirectory($root, 0775, true);
            }

            if (! is_writable($root)) {
                return [
                    'ok' => false,
                    'message' => "مسیر لاگ «{$root}» قابل نوشتن نیست.",
                ];
            }

            return ['ok' => true, 'message' => "مسیر لاگ آماده: {$root}"];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => "ساخت مسیر لاگ ناموفق: {$e->getMessage()}",
            ];
        }
    }

    /**
     * وقتی APP_LOG_ROOT خارج از storage/logs است، symlink می‌سازد تا Laravel و آرشیو یکسان بمانند.
     */
    public static function ensureStorageLogsSymlink(): bool
    {
        $target = self::logsRoot();
        $link = storage_path('logs');

        if (realpath($target) === realpath($link)) {
            return true;
        }

        if (file_exists($link) && ! is_link($link)) {
            return false;
        }

        if (is_link($link) && realpath(readlink($link) ?: '') === realpath($target)) {
            return true;
        }

        if (is_link($link) || file_exists($link)) {
            @unlink($link);
        }

        if (! File::isDirectory($target)) {
            File::makeDirectory($target, 0775, true);
        }

        try {
            return symlink($target, $link);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function k8sHint(): string
    {
        return 'APP_LOG_ROOT=/data/baharfc/storage/logs و mkdir + chown مشابه دیسک آپلود؛ سپس php artisan baharfc:post-deploy';
    }
}