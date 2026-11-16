<?php

namespace Tests\Feature;

use App\Enums\BlogCommentStatus;
use App\Enums\BlogPostStatus;
use App\Livewire\Shop\BlogPostComments;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BlogCommentsTest extends TestCase
{
    use RefreshDatabase;

    private function publishedPost(): BlogPost
    {
        return BlogPost::query()->create([
            'title' => 'مقاله تست',
            'slug' => 'test-post',
            'body' => '<p>محتوا</p>',
            'status' => BlogPostStatus::Published,
            'allow_comments' => true,
            'published_at' => now(),
        ]);
    }

    public function test_guest_comment_submission_stays_pending(): void
    {
        $post = $this->publishedPost();

        Livewire::test(BlogPostComments::class, ['post' => $post])
            ->set('authorName', 'کاربر')
            ->set('authorEmail', 'user@example.com')
            ->set('body', 'نظر تستی')
            ->call('submit')
            ->assertSet('statusMessage', 'نظر شما ثبت شد و پس از تأیید مدیر نمایش داده می‌شود.');

        $this->assertDatabaseHas('blog_comments', [
            'blog_post_id' => $post->id,
            'author_email' => 'user@example.com',
            'status' => BlogCommentStatus::Pending->value,
        ]);
    }

    public function test_approved_comment_visible_on_post_page(): void
    {
        $post = $this->publishedPost();

        BlogComment::query()->create([
            'blog_post_id' => $post->id,
            'author_name' => 'نویسنده',
            'author_email' => 'a@example.com',
            'body' => 'نظر تأیید شده',
            'status' => BlogCommentStatus::Approved,
        ]);

        $this->get(route('blog.show', 'test-post'))
            ->assertOk()
            ->assertSee('نظر تأیید شده')
            ->assertSee('نظرات (1)');
    }

    public function test_scheduled_post_not_visible_before_publish_time(): void
    {
        BlogPost::query()->create([
            'title' => 'آینده',
            'slug' => 'future-post',
            'body' => 'متن',
            'status' => BlogPostStatus::Scheduled,
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('blog.show', 'future-post'))->assertNotFound();
    }
}