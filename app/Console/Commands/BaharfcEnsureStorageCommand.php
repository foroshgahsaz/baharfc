<?php

namespace App\Console\Commands;

use App\Support\PublicUploadStorage;
use Illuminate\Console\Command;

class BaharfcEnsureStorageCommand extends Command
{
    protected $signature = 'baharfc:ensure-storage';

    protected $description = 'ساخت پوشه‌های آپلود public (shop، products، …) و تست نوشتن — برای K8s و FILESYSTEM_PUBLIC_ROOT';

    public function handle(): int
    {
        $root = PublicUploadStorage::publicRoot();
        $this->components->info("دیسک public: {$root}");

        $result = PublicUploadStorage::ensureUploadDirectories();

        if ($result['ok']) {
            $this->components->success($result['message']);

            return self::SUCCESS;
        }

        $this->components->error($result['message']);
        $this->line('');
        $this->line(PublicUploadStorage::k8sFixHint($root));

        return self::FAILURE;
    }
}