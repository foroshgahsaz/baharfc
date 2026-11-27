<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_categories')) {
            Schema::table('blog_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('blog_categories', 'parent_id')) {
                    $table->foreignId('parent_id')->nullable()->after('id')->constrained('blog_categories')->nullOnDelete();
                }
                if (! Schema::hasColumn('blog_categories', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
                if (! Schema::hasColumn('blog_categories', 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('description');
                }
                if (! Schema::hasColumn('blog_categories', 'meta_description')) {
                    $table->string('meta_description', 500)->nullable()->after('meta_title');
                }
            });
        }

        if (! Schema::hasTable('blog_tags')) {
            Schema::create('blog_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('blog_post_blog_tag')) {
            Schema::create('blog_post_blog_tag', function (Blueprint $table) {
                $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
                $table->foreignId('blog_tag_id')->constrained()->cascadeOnDelete();
                $table->primary(['blog_post_id', 'blog_tag_id']);
            });
        }

        if (Schema::hasTable('blog_posts') && Schema::hasTable('blog_tags')) {
            $this->migrateLegacyCommaTags();
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_blog_tag');
        Schema::dropIfExists('blog_tags');

        if (Schema::hasTable('blog_categories')) {
            Schema::table('blog_categories', function (Blueprint $table) {
                foreach (['meta_description', 'meta_title', 'description', 'parent_id'] as $column) {
                    if (Schema::hasColumn('blog_categories', $column)) {
                        if ($column === 'parent_id') {
                            $table->dropConstrainedForeignId('parent_id');
                        } else {
                            $table->dropColumn($column);
                        }
                    }
                }
            });
        }
    }

    private function migrateLegacyCommaTags(): void
    {
        if (! Schema::hasColumn('blog_posts', 'tags')) {
            return;
        }

        $posts = DB::table('blog_posts')
            ->select(['id', 'tags'])
            ->whereNotNull('tags')
            ->where('tags', '!=', '')
            ->get();

        foreach ($posts as $post) {
            foreach (preg_split('/\s*,\s*/u', (string) $post->tags) ?: [] as $name) {
                $name = trim($name);
                if ($name === '') {
                    continue;
                }

                $slug = Str::slug($name);
                if ($slug === '') {
                    $slug = 'tag-'.substr(md5($name), 0, 12);
                }

                $tagId = DB::table('blog_tags')->where('slug', $slug)->value('id');
                if (! $tagId) {
                    $tagId = DB::table('blog_tags')->insertGetId([
                        'name' => $name,
                        'slug' => $slug,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('blog_post_blog_tag')->insertOrIgnore([
                    'blog_post_id' => $post->id,
                    'blog_tag_id' => $tagId,
                ]);
            }
        }
    }
};