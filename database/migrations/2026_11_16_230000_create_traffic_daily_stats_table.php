<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('stat_date');
            $table->string('entity_type', 32);
            $table->string('entity_key', 191);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();

            $table->unique(['stat_date', 'entity_type', 'entity_key']);
            $table->index(['entity_type', 'stat_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_daily_stats');
    }
};