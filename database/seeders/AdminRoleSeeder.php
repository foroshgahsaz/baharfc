<?php

namespace Database\Seeders;

use App\Models\AdminRole;
use App\Support\AdminPermissions;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $presets = AdminPermissions::presetRolePermissions();

        AdminRole::query()->updateOrCreate(
            ['slug' => 'author'],
            [
                'name' => 'نویسنده',
                'description' => 'مدیریت مقالات، دسته‌های بلاگ و نظرات',
                'permissions' => $presets['author'],
                'is_system' => true,
            ],
        );

        AdminRole::query()->updateOrCreate(
            ['slug' => 'shop_manager'],
            [
                'name' => 'مدیر فروشگاه',
                'description' => 'محصولات، سفارش‌ها و عملیات فروش',
                'permissions' => $presets['shop_manager'],
                'is_system' => true,
            ],
        );
    }
}