<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_settings', function (Blueprint $table) {
            $table->boolean('contact_form_enabled')->default(true)->after('contact_email');
            $table->string('contact_form_title')->nullable()->after('contact_form_enabled');
            $table->text('contact_form_intro')->nullable()->after('contact_form_title');
            $table->string('contact_form_success_message')->nullable()->after('contact_form_intro');
            $table->boolean('contact_form_show_phone')->default(true)->after('contact_form_success_message');
            $table->boolean('contact_form_require_phone')->default(false)->after('contact_form_show_phone');
            $table->boolean('contact_form_show_subject')->default(true)->after('contact_form_require_phone');
            $table->boolean('contact_form_require_subject')->default(false)->after('contact_form_show_subject');
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status', 32)->default('new');
            $table->string('ip', 45)->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');

        Schema::table('shop_settings', function (Blueprint $table) {
            $table->dropColumn([
                'contact_form_enabled',
                'contact_form_title',
                'contact_form_intro',
                'contact_form_success_message',
                'contact_form_show_phone',
                'contact_form_require_phone',
                'contact_form_show_subject',
                'contact_form_require_subject',
            ]);
        });
    }
};