<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_on_home')->default(false)->after('is_active');
            $table->string('home_caption', 120)->nullable()->after('show_on_home');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['show_on_home', 'home_caption']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};