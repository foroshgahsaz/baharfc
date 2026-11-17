<?php

$explicit = env('APP_BACKUP_ROOT');

if (filled($explicit)) {
    $root = rtrim((string) $explicit, '/\\');
} else {
    $publicRoot = env('FILESYSTEM_PUBLIC_ROOT');
    if (filled($publicRoot)) {
        // کنار public روی همان volume: .../storage/app/backups — نه .../storage/backups که معمولاً برای www-data قابل نوشتن نیست
        $root = rtrim(dirname((string) $publicRoot), '/\\').'/backups';
    } else {
        $root = storage_path('app/backups');
    }
}

return [
    'root' => $root,
    'keep' => (int) env('APP_BACKUP_KEEP', 4),
    'retain_days' => (int) env('APP_BACKUP_RETAIN_DAYS', 2),
];
