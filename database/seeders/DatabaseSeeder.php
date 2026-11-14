<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@baharfc.local'],
            [
                'name' => 'مدیر فروشگاه',
                'username' => 'admin',
                'password' => 'password',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        $this->call([
            DemoCatalogSeeder::class,
            CartCatalogSeeder::class,
        ]);
    }
}
