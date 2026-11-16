<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BaharfcPostDeployCommand extends Command
{
    protected $signature = 'baharfc:post-deploy {--seed-menu : فقط در صورت خالی بودن، منوی هدر را seed کنید}';

    protected $description = 'پس از git pull: migrate، پاک‌سازی کش، storage:link — برای جلوگیری از خطای ۵۰۰';

    public function handle(): int
    {
        $this->components->info('اجرای migrate...');
        $this->call('migrate', ['--force' => true]);

        if ($this->option('seed-menu')) {
            $this->components->info('Seed منوی فروشگاه (در صورت خالی بودن)...');
            $this->call('db:seed', ['--class' => 'Database\\Seeders\\ShopMenuSeeder', '--force' => true]);
        }

        $this->components->info('storage:link');
        $this->call('storage:link');

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