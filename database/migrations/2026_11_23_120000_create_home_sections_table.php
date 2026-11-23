<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('subtitle', 240)->nullable();
            $table->string('style', 32)->default('style_1');
            $table->string('source', 32)->default('category');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('product_limit')->default(8);
            $table->string('more_label', 80)->nullable();
            $table->string('more_url', 2048)->nullable();
            $table->string('position_after', 40)->default('special_deals');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('home_section_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['home_section_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_section_product');
        Schema::dropIfExists('home_sections');
    }
};