<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('payment_mode', 20)->default('sandbox');
            $table->timestamps();
        });

        DB::table('payment_settings')->insert([
            'payment_mode' => config('payment.mode', 'sandbox'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('label');
            $table->boolean('is_enabled')->default(true);
            $table->text('credentials')->nullable();
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['slug' => 'mellat', 'label' => 'بانک ملت (به‌پرداخت)', 'is_enabled' => true, 'credentials' => null, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'parsian', 'label' => 'بانک پارسیان', 'is_enabled' => true, 'credentials' => null, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'digipay', 'label' => 'دیجی‌پی', 'is_enabled' => true, 'credentials' => null, 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'snapppay', 'label' => 'اسنپ‌پی', 'is_enabled' => true, 'credentials' => null, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('payment_gateway_settings')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_settings');
        Schema::dropIfExists('payment_settings');
    }
};
