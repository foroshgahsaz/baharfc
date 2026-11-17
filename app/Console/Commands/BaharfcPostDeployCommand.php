<?php

namespace App\Console\Commands;

use App\Support\FrameworkWritablePaths;
use App\Support\PublicUploadStorage;
use Illuminate\Console\Command;

class BaharfcPostDeployCommand extends Command
{
    protected $signature = 'baharfc:post-deploy {--seed-menu : فقط در صورت خالی بودن، منوی هدر را seed کنید}';

    protected $description = 'پس از git pull: migrate، پاک‌سازی کش، storage:link — برای جلوگیری از خطای ۵۰۰';

    public function handle(): int
    {
        $this->components->info('بررسی bootstrap/cache و storage/framework...');
        $pathStatus = FrameworkWritablePaths::ensurePresent();
        foreach ($pathStatus as $relative => $writable) {
            if ($writable) {
                continue;
            }

            $this->components->error("پوشهٔ «{$relative}» قابل نوشتن نیست.");
        }

        if (! FrameworkWritablePaths::isHealthy()) {
            $this->components->warn('دستور تعمیر مجوز (یک‌بار روی سرور با root):');
            $this->line(FrameworkWritablePaths::repairShellHint());
        }

        $this->components->info('اجرای migrate...');
        $this->call('migrate', ['--force' => true]);

        if (\Illuminate\Support\Facades\Schema::hasTable('admin_roles')) {
            $this->components->info('نقش‌های پیش‌فرض پنل (در صورت وجود کد)...');
            $this->call('baharfc:seed-admin-roles');
        }

        if ($this->option('seed-menu')) {
            $this->components->info('Seed منوی فروشگاه (در صورت خالی بودن)...');
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\ShopMenuSeeder', '--force' => true]);
        }

        $storage = PublicUploadStorage::ensureUploadDirectories();
        if ($storage['ok']) {
            $this->components->success($storage['message']);
        } else {
            $this->components->warn($storage['message']);
            $this->line(PublicUploadStorage::k8sFixHint($storage['root']));
        }

        $symlink = PublicUploadStorage::symlinkDiagnostics();
        if (! $symlink['ok']) {
            $this->components->warn($symlink['message']);
        }

        $this->components->info('اتصال public/storage به FILESYSTEM_PUBLIC_ROOT');
        if (! PublicUploadStorage::ensurePublicSymlink()) {
            $this->call('storage:link', ['--force' => true]);
        }

        $afterLink = PublicUploadStorage::symlinkDiagnostics();
        if ($afterLink['ok']) {
            $this->components->success($afterLink['message']);
        } else {
            $this->components->error($afterLink['message']);
        }

        $this->components->info('پاک‌سازی کش...');
        $this->call('optimize:clear');

        try {
            $this->call('filament:optimize-clear');
        } catch (\Throwable) {
            // Filament ممکن است در برخی نصب‌ها غیرفعال باشد
        }

        $this->components->success('دیپلوی اپلیکیشن تکمیل شد. یک‌بار صفحه اصلی و /admin را تست کنید.');

        return self::SUCCESS;
    }
}