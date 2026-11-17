<?php

namespace App\Console\Commands;

use App\Services\Admin\BackupService;
use App\Support\PersistentBackupStorage;
use Illuminate\Console\Command;

class BaharfcBackupCommand extends Command
{
    protected $signature = 'baharfc:backup {--type=all : all|database|files} {--only-prune : فقط پاک‌سازی آرشیوهای قدیمی}';

    protected $description = 'بک‌آپ دیتابیس و فایل‌ها روی دیسک پایدار (حداکثر ۴ نسخه، ۲ روز)';

    public function handle(BackupService $backups): int
    {
        $ready = PersistentBackupStorage::ensureBackupsDirectory();
        if (! $ready['ok']) {
            $this->components->error($ready['message']);
            $this->line(PersistentBackupStorage::k8sHint());

            return self::FAILURE;
        }

        if ($this->option('only-prune')) {
            $deleted = $backups->prune();
            $this->components->info("آرشیوهای قدیمی حذف شد: {$deleted}");

            return self::SUCCESS;
        }

        $type = (string) $this->option('type');
        $ran = false;

        if (in_array($type, ['all', 'database'], true)) {
            $result = $backups->backupDatabase();
            $this->report($result);
            $ran = true;
            if (! $result['ok']) {
                return self::FAILURE;
            }
        }

        if (in_array($type, ['all', 'files'], true)) {
            $result = $backups->backupFiles();
            $this->report($result);
            $ran = true;
            if (! $result['ok']) {
                return self::FAILURE;
            }
        }

        if (! $ran && ! $this->option('prune')) {
            $this->components->error('نوع نامعتبر. از all، database یا files استفاده کنید.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @param  array{ok: bool, message: string, path?: string}  $result
     */
    protected function report(array $result): void
    {
        if ($result['ok']) {
            $this->components->success($result['message'].(isset($result['path']) ? ' — '.$result['path'] : ''));

            return;
        }

        $this->components->error($result['message']);
    }
}
