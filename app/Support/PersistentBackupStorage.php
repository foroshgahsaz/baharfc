<?php

namespace App\Support;

class PersistentBackupStorage
{
    protected static ?string $resolvedRoot = null;

    public static function flushResolved(): void
    {
        self::$resolvedRoot = null;
    }

    public static function backupsRoot(): string
    {
        if (self::$resolvedRoot !== null) {
            return self::$resolvedRoot;
        }

        $preferred = (string) config('backup.root', '');
        $chosen = PersistentVolume::firstWritableDirectory(
            PersistentVolume::backupCandidates($preferred !== '' ? $preferred : null)
        );

        self::$resolvedRoot = $chosen ?: rtrim($preferred !== '' ? $preferred : storage_path('app/backups'), '/\\');

        return self::$resolvedRoot;
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
        self::flushResolved();
        $root = self::backupsRoot();

        try {
            foreach ([$root, self::typeDirectory('database'), self::typeDirectory('files')] as $path) {
                if (! PersistentVolume::ensureWritableDirectory($path)) {
                    return [
                        'ok' => false,
                        'message' => "مسیر بک‌آپ «{$path}» قابل نوشتن نیست. ".self::k8sHint(),
                    ];
                }
            }

            return ['ok' => true, 'message' => "مسیر بک‌آپ پایدار آماده: {$root}"];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => "ساخت مسیر بک‌آپ ناموفق: {$e->getMessage()}",
            ];
        }
    }

    public static function k8sHint(): string
    {
        $public = PersistentVolume::publicRoot() ?: '/data/baharfc/storage/app/public';
        $sibling = dirname($public).'/backups';

        return "بک‌آپ باید روی volume تصاویر باشد (نه داخل پاد). mkdir -p {$sibling} && chown -R www-data:www-data {$sibling} && chmod -R ug+rwx {$sibling}";
    }
}
