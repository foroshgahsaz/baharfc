<?php

namespace App\Console\Commands;

use App\Support\PublicUploadStorage;
use Illuminate\Console\Command;

class BaharfcRepairStorageLinkCommand extends Command
{
    protected $signature = 'baharfc:repair-storage-link|baharfc:repair-storage';

    protected $description = 'اگر /storage/... در سایت ۴۰۴ می‌دهد: public/storage را به FILESYSTEM_PUBLIC_ROOT وصل می‌کند';

    public function handle(): int
    {
        $before = PublicUploadStorage::symlinkDiagnostics();
        $this->line("دیسک public: ".PublicUploadStorage::publicRoot());
        $this->line("هدف symlink: ".PublicUploadStorage::configuredSymlinkTarget());

        if ($before['ok']) {
            $this->components->success($before['message']);

            return self::SUCCESS;
        }

        $this->components->warn($before['message']);
        PublicUploadStorage::ensureUploadDirectories();
        $repaired = PublicUploadStorage::ensurePublicSymlink();
        if (! $repaired) {
            $this->call('storage:link', ['--force' => true]);
        }

        $after = PublicUploadStorage::symlinkDiagnostics();
        if ($after['ok']) {
            $this->components->success('تصاویر باید از مسیر /storage/... سرو شوند. اگر هنوز ۴۰۴ است، فایل روی دیسک نیست — لوگو را دوباره آپلود کنید.');

            return self::SUCCESS;
        }

        $this->components->error($after['message']);
        $this->line(PublicUploadStorage::k8sFixHint(PublicUploadStorage::publicRoot()));

        return self::FAILURE;
    }
}