<?php

namespace Tests\Feature;

use App\Livewire\Shop\NewsletterSubscribe;
use App\Models\Banner;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MarketingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_active_banners(): void
    {
        Banner::query()->create([
            'title' => 'جشنواره پاییز',
            'subtitle' => 'تا ۳۰٪ تخفیف',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('جشنواره پاییز');
    }

    public function test_newsletter_subscribe(): void
    {
        Livewire::test(NewsletterSubscribe::class)
            ->set('email', 'news@example.com')
            ->call('subscribe')
            ->assertSet('statusMessage', 'عضویت شما در خبرنامه ثبت شد.');

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'news@example.com']);
    }

    public function test_published_blog_post_visible(): void
    {
        BlogPost::query()->create([
            'title' => 'راهنمای خرید',
            'slug' => 'buying-guide',
            'body' => 'متن مقاله',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get(route('blog.show', 'buying-guide'))
            ->assertOk()
            ->assertSee('راهنمای خرید');
    }
}
