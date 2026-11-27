<?php

namespace Tests\Feature;

use App\Enums\BlogPostStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogWordPressFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function publishPost(array $overrides = []): BlogPost
    {
        return BlogPost::query()->create(array_merge([
            'title' => 'راهنمای خرید',
            'slug' => 'buying-guide',
            'body' => '<p>متن کامل مقاله برای تست بلاگ.</p>',
            'status' => BlogPostStatus::Published,
            'published_at' => now()->subDay(),
            'is_featured' => true,
        ], $overrides));
    }

    public function test_blog_index_renders_wordpress_layout(): void
    {
        $this->publishPost();

        $response = $this->get(route('blog.index'));
        $response->assertOk();
        $response->assertSee('fs-blog-layout', false);
        $response->assertSee('fs-blog-sidebar', false);
        $response->assertSee('راهنمای خرید', false);
    }

    public function test_category_tag_author_archives_and_feed(): void
    {
        $author = User::factory()->create(['name' => 'نویسنده تست']);
        $category = BlogCategory::query()->create([
            'name' => 'آموزش',
            'slug' => 'tutorials',
            'is_active' => true,
        ]);
        $tag = BlogTag::query()->create(['name' => 'مد', 'slug' => 'fashion']);

        $post = $this->publishPost([
            'slug' => 'fashion-guide',
            'blog_category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        $post->tags()->sync([$tag->id]);

        $this->get(route('blog.category', 'tutorials'))
            ->assertOk()
            ->assertSee('fashion-guide', false);

        $this->get(route('blog.tag', 'fashion'))
            ->assertOk()
            ->assertSee('fashion-guide', false);

        $this->get(route('blog.author', $author))
            ->assertOk()
            ->assertSee('fashion-guide', false);

        $year = now()->subDay()->year;
        $month = now()->subDay()->month;

        $this->get(route('blog.archive', ['year' => $year, 'month' => $month]))
            ->assertOk()
            ->assertSee('fashion-guide', false);

        $this->get(route('blog.search', ['q' => 'راهنمای']))
            ->assertOk()
            ->assertSee('fashion-guide', false);

        $feed = $this->get(route('blog.feed'));
        $feed->assertOk();
        $feed->assertHeader('content-type', 'application/rss+xml; charset=UTF-8');
        $feed->assertSee('fashion-guide', false);
    }

    public function test_post_page_has_seo_and_related_markup(): void
    {
        $post = $this->publishPost(['slug' => 'seo-post', 'meta_title' => 'عنوان سئو']);

        $response = $this->get(route('blog.show', 'seo-post'));
        $response->assertOk();
        $response->assertSee('og:title', false);
        $response->assertSee('fs-blog-single', false);
        $response->assertSee('schema.org/BlogPosting', false);
    }
}