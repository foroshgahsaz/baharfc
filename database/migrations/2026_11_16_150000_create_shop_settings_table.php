<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->string('logo_wordmark')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('site_description')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('accent_color', 20)->nullable();
            $table->boolean('maintenance_enabled')->default(false);
            $table->text('maintenance_message')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('contact_email')->nullable();
            $table->text('footer_text')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_settings');
    }
};