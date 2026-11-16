<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class BaharfcSeedAdminRolesCommand extends Command
{
    protected $signature = 'baharfc:seed-admin-roles';

    protected $description = 'ایجاد یا به‌روزرسانی نقش‌های پیش‌فرض (نویسنده، مدیر فروشگاه)';

    public function handle(): int
    {
        if (! Schema::hasTable('admin_roles')) {
            $this->components->error('جدول admin_roles وجود ندارد. ابتدا php artisan migrate --force را اجرا کنید.');

            return self::FAILURE;
        }

        $seederClass = \Database\Seeders\AdminRoleSeeder::class;
        if (! class_exists($seederClass)) {
            $this->components->error('فایل AdminRoleSeeder روی سرور نیست — نسخهٔ کد قدیمی است.');
            $this->line('آخرین main (شامل PR #46 نقش‌ها) را deploy کنید، سپس composer install و دوباره این دستور را بزنید.');

            return self::FAILURE;
        }

        $this->call('db:seed', [
            '--class' => $seederClass,
            '--force' => true,
        ]);

        $this->components->success('نقش‌های پیش‌فرض آماده است.');

        return self::SUCCESS;
    }
}