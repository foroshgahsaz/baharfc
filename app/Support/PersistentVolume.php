<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * مسیرهای پایدار روی همان volume تصاویر (FILESYSTEM_PUBLIC_ROOT)
 * تا لاگ و بک‌آپ با دیپلوی پاک نشوند و برای www-data قابل نوشتن باشند.
 */
class PersistentVolume
{
    /**
     * @return list<string>
     */
    public static function backupCandidates(?string $preferred = null): array
    {
        $candidates = [];
        if (filled($preferred)) {
            $candidates[] = rtrim((string) $preferred, '/\\');
        }

        $public = self::publicRoot();
        if ($public !== '' && ! self::isDefaultPublicRoot($public)) {
            $candidates[] = dirname($public).DIRECTORY_SEPARATOR.'backups';
            $candidates[] = $public.DIRECTORY_SEPARATOR.'.baharfc'.DIRECTORY_SEPARATOR.'backups';
        }

        $candidates[] = storage_path('app/backups');

        return array_values(array_unique($candidates));
    }

    /**
     * @return list<string>
     */
    public static function logCandidates(?string $preferred = null): array
    {
        $candidates = [];
        if (filled($preferred)) {
            $candidates[] = rtrim((string) $preferred, '/\\');
        }

        $public = self::publicRoot();
        if ($public !== '' && ! self::isDefaultPublicRoot($public)) {
            $candidates[] = dirname($public).DIRECTORY_SEPARATOR.'logs';
            $candidates[] = $public.DIRECTORY_SEPARATOR.'.baharfc'.DIRECTORY_SEPARATOR.'logs';
        }

        $candidates[] = storage_path('logs');

        return array_values(array_unique($candidates));
    }

    public static function publicRoot(): string
    {
        $configured = (string) (config('filesystems.disks.public.root') ?: env('FILESYSTEM_PUBLIC_ROOT', ''));

        return rtrim($configured, '/\\');
    }

    public static function isDefaultPublicRoot(string $root): bool
    {
        $default = rtrim(storage_path('app/public'), '/\\');
        $resolvedRoot = realpath($root) ?: $root;
        $resolvedDefault = realpath($default) ?: $default;

        return $resolvedRoot === $resolvedDefault;
    }

    public static function ensureWritableDirectory(string $path): bool
    {
        $path = rtrim($path, '/\\');
        if ($path === '') {
            return false;
        }

        try {
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0775, true);
            }
        } catch (\Throwable) {
            return false;
        }

        if (! File::isDirectory($path)) {
            return false;
        }

        self::relaxPermissions($path);
        self::denyWebAccessIfHidden($path);

        return self::canWrite($path);
    }

    public static function firstWritableDirectory(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (! is_string($candidate) || $candidate === '') {
                continue;
            }
            if (self::ensureWritableDirectory($candidate)) {
                return rtrim($candidate, '/\\');
            }
        }

        return null;
    }

    public static function relaxPermissions(string $path): void
    {
        @chmod($path, 0775);

        if (function_exists('posix_getuid') && posix_getuid() === 0 && function_exists('posix_getpwnam')) {
            foreach (['www-data', 'nginx', 'apache', 'http'] as $user) {
                $info = @posix_getpwnam($user);
                if (! is_array($info)) {
                    continue;
                }
                @chown($path, $info['uid']);
                @chgrp($path, $info['gid']);
                break;
            }
        }

        if (! self::canWrite($path)) {
            @chmod($path, 0777);
        }
    }

    protected static function denyWebAccessIfHidden(string $path): void
    {
        if (! str_contains($path, DIRECTORY_SEPARATOR.'.baharfc')) {
            return;
        }

        $marker = dirname($path).DIRECTORY_SEPARATOR.'.htaccess';
        if (is_file($marker)) {
            return;
        }

        @file_put_contents($marker, "Require all denied\nDeny from all\n");
    }

    public static function canWrite(string $path): bool
    {
        if (! is_dir($path) || ! is_writable($path)) {
            return false;
        }

        $probe = $path.DIRECTORY_SEPARATOR.'.write_probe_'.uniqid('', true);
        try {
            if (@file_put_contents($probe, 'ok') === false) {
                return false;
            }
            @unlink($probe);

            return true;
        } catch (\Throwable) {
            @unlink($probe);

            return false;
        }
    }

    public static function migrateFiles(string $from, string $to): void
    {
        $fromReal = realpath($from);
        $toReal = realpath($to) ?: $to;
        if ($fromReal === false || $fromReal === $toReal || ! is_dir($fromReal)) {
            return;
        }

        if (! File::isDirectory($to)) {
            File::makeDirectory($to, 0775, true);
        }

        foreach (File::files($fromReal) as $file) {
            $dest = $to.DIRECTORY_SEPARATOR.$file->getFilename();
            if (is_file($dest)) {
                continue;
            }
            @copy($file->getPathname(), $dest);
        }
    }
}
