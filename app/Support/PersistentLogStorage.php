<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class PersistentLogStorage
{
    protected static ?string $resolvedRoot = null;

    public static function flushResolved(): void
    {
        self::$resolvedRoot = null;
    }

    public static function logsRoot(): string
    {
        if (self::$resolvedRoot !== null) {
            return self::$resolvedRoot;
        }

        $preferred = (string) (config('logging.persistent_root') ?: env('APP_LOG_ROOT', ''));
        $chosen = PersistentVolume::firstWritableDirectory(
            PersistentVolume::logCandidates($preferred !== '' ? $preferred : null)
        );

        self::$resolvedRoot = $chosen ?: storage_path('logs');

        return self::$resolvedRoot;
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
        self::flushResolved();
        $root = self::logsRoot();

        if (! PersistentVolume::canWrite($root)) {
            return [
                'ok' => false,
                'message' => "مسیر لاگ «{$root}» قابل نوشتن نیست.",
            ];
        }

        return ['ok' => true, 'message' => "مسیر لاگ پایدار آماده: {$root}"];
    }

    /**
     * storage/logs را به دیسک پایدار لینک می‌کند و فایل‌های قبلی را منتقل می‌کند
     * تا بعد از دیپلوی پاک نشوند.
     */
    public static function ensureStorageLogsSymlink(): bool
    {
        $target = self::logsRoot();
        PersistentVolume::ensureWritableDirectory($target);

        $link = storage_path('logs');
        $targetReal = realpath($target) ?: $target;
        $linkReal = realpath($link) ?: null;

        if ($linkReal !== null && $linkReal === $targetReal) {
            return true;
        }

        if (is_link($link)) {
            $current = realpath($link);
            if ($current === $targetReal) {
                return true;
            }
            @unlink($link);
        } elseif (is_dir($link)) {
            PersistentVolume::migrateFiles($link, $target);
            $retired = $link.'.replaced-'.time();
            if (! @rename($link, $retired)) {
                try {
                    File::deleteDirectory($link);
                } catch (\Throwable) {
                    return false;
                }
            }
        } elseif (file_exists($link)) {
            @unlink($link);
        }

        if (! File::isDirectory($target)) {
            PersistentVolume::ensureWritableDirectory($target);
        }

        try {
            return symlink($target, $link);
        } catch (\Throwable) {
            return false;
        }
    }

    public static function k8sHint(): string
    {
        $public = PersistentVolume::publicRoot() ?: '/data/baharfc/storage/app/public';

        return 'لاگ باید روی همان volume تصاویر باشد. FILESYSTEM_PUBLIC_ROOT='.$public
            .' — پوشهٔ '.dirname($public).'/logs یا APP_LOG_ROOT را mkdir+chown کنید؛ سپس php artisan baharfc:post-deploy';
    }
}
