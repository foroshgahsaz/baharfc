<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class PersistentBackupStorage
{
    public static function backupsRoot(): string
    {
        return rtrim((string) config('backup.root', storage_path('app/backups')), '/\\');
    }

    public static function typeDirectory(string $type): string
    {
        $type = $type === 'files' ? 'files' : 'database';

        return self::backupsRoot().DIRECTORY_SEPARATOR.$type;
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public static function ensureBackupsDirectory(): array
    {
        $root = self::backupsRoot();

        try {
            foreach ([$root, self::typeDirectory('database'), self::typeDirectory('files')] as $path) {
                if (! File::isDirectory($path)) {
                    File::makeDirectory($path, 0775, true);
                }
            }

            if (! is_writable($root)) {
                return [
                    'ok' => false,
                    'message' => "مسیر بک‌آپ «{$root}» قابل نوشتن نیست.",
                ];
            }

            return ['ok' => true, 'message' => "مسیر بک‌آپ آماده: {$root}"];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => "ساخت مسیر بک‌آپ ناموفق: {$e->getMessage()}",
            ];
        }
    }

    public static function k8sHint(): string
    {
        $root = self::backupsRoot();

        return "APP_BACKUP_ROOT={$root} — mkdir + chown مشابه دیسک آپلود؛ سپس php artisan baharfc:post-deploy";
    }
}
