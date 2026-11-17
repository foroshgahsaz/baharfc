<?php

namespace App\Console\Commands;

use App\Support\FrameworkWritablePaths;
use App\Support\PublicUploadStorage;
use Illuminate\Console\Command;

class BaharfcEnsureStorageCommand extends Command
{
    protected $signature = 'baharfc:ensure-storage';

    protected $description = 'ساخت پوشه‌های آپلود public (shop، products، …) و تست نوشتن — برای K8s و FILESYSTEM_PUBLIC_ROOT';

    public function handle(): int
    {
        $pathStatus = FrameworkWritablePaths::ensurePresent();
        foreach ($pathStatus as $relative => $writable) {
            $this->components->info("{$relative}: ".($writable ? 'OK' : 'غیرقابل نوشتن'));
        }

        if (! FrameworkWritablePaths::isHealthy()) {
            $this->components->warn(FrameworkWritablePaths::repairShellHint());
        }

        $root = PublicUploadStorage::publicRoot();
        $this->components->info("دیسک public: {$root}");

        $result = PublicUploadStorage::ensureUploadDirectories();
        $symlink = PublicUploadStorage::symlinkDiagnostics();

        if ($result['ok']) {
            $this->components->success($result['message']);
        } else {
            $this->components->error($result['message']);
        }

        if ($symlink['ok']) {
            $this->components->success($symlink['message']);
        } else {
            $this->components->error($symlink['message']);
            $this->components->warn('اجرا: php artisan storage:link --force');
        }

        if ($result['ok'] && $symlink['ok']) {
            return self::SUCCESS;
        }

        $this->line('');
        $this->line(PublicUploadStorage::k8sFixHint($root));

        return self::FAILURE;
    }
}