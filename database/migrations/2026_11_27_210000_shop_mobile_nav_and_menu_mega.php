<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_items') && ! Schema::hasColumn('menu_items', 'is_mega_menu')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->boolean('is_mega_menu')->default(false)->after('open_in_new_tab');
            });
        }

        if (Schema::hasTable('shop_settings')) {
            Schema::table('shop_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('shop_settings', 'mobile_header_search_icon')) {
                    $table->boolean('mobile_header_search_icon')->default(true);
                }
                if (! Schema::hasColumn('shop_settings', 'mobile_bottom_nav_enabled')) {
                    $table->boolean('mobile_bottom_nav_enabled')->default(true);
                }
                if (! Schema::hasColumn('shop_settings', 'mobile_bottom_nav_items')) {
                    $table->json('mobile_bottom_nav_items')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('menu_items') && Schema::hasColumn('menu_items', 'is_mega_menu')) {
            Schema::table('menu_items', function (Blueprint $table) {
                $table->dropColumn('is_mega_menu');
            });
        }

        if (Schema::hasTable('shop_settings')) {
            Schema::table('shop_settings', function (Blueprint $table) {
                $columns = ['mobile_header_search_icon', 'mobile_bottom_nav_enabled', 'mobile_bottom_nav_items'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('shop_settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};