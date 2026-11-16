<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('blog_category_id')->constrained('users')->nullOnDelete();
            $table->string('featured_image_path')->nullable()->after('excerpt');
            $table->string('status', 32)->default('draft')->after('body');
            $table->string('meta_title')->nullable()->after('status');
            $table->string('meta_description', 500)->nullable()->after('meta_title');
            $table->string('tags')->nullable()->after('meta_description');
            $table->boolean('allow_comments')->default(true)->after('tags');
            $table->boolean('is_featured')->default(false)->after('allow_comments');
        });

        if (Schema::hasColumn('blog_posts', 'is_published')) {
            DB::table('blog_posts')->where('is_published', true)->update(['status' => 'published']);
        }

        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('blog_comments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->string('author_email')->nullable();
            $table->string('author_ip', 45)->nullable();
            $table->text('body');
            $table->string('status', 32)->default('pending');
            $table->timestamps();

            $table->index(['blog_post_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_comments');

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_id');
            $table->dropColumn([
                'featured_image_path',
                'status',
                'meta_title',
                'meta_description',
                'tags',
                'allow_comments',
                'is_featured',
            ]);
        });
    }
};