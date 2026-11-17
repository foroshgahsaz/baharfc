<?php

$explicit = env('APP_BACKUP_ROOT');

if (filled($explicit)) {
    $root = rtrim((string) $explicit, '/\\');
} else {
    $publicRoot = env('FILESYSTEM_PUBLIC_ROOT');
    if (filled($publicRoot)) {
        $root = rtrim(dirname(dirname((string) $publicRoot)), '/\\').'/backups';
    } else {
        $root = storage_path('app/backups');
    }
}

return [
    'root' => $root,
    'keep' => (int) env('APP_BACKUP_KEEP', 4),
    'retain_days' => (int) env('APP_BACKUP_RETAIN_DAYS', 2),
];
