<?php

namespace App\Livewire\Shop;

use App\Enums\BlogCommentStatus;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class BlogPostComments extends Component
{
    public BlogPost $post;

    public string $authorName = '';

    public string $authorEmail = '';

    public string $body = '';

    public ?int $parentId = null;

    public ?string $statusMessage = null;

    public function mount(BlogPost $post): void
    {
        $this->post = $post;

        if (Auth::check()) {
            $this->authorName = Auth::user()->name ?? '';
            $this->authorEmail = Auth::user()->email ?? '';
        }
    }

    public function setReplyTo(int $commentId): void
    {
        $this->parentId = $commentId;
        $this->statusMessage = null;
    }

    public function cancelReply(): void
    {
        $this->parentId = null;
    }

    public function submit(): void
    {
        if (! $this->post->allow_comments) {
            return;
        }

        $rules = [
            'body' => ['required', 'string', 'min:3', 'max:5000'],
        ];

        if (! Auth::check()) {
            $rules['authorName'] = ['required', 'string', 'max:120'];
            $rules['authorEmail'] = ['required', 'email', 'max:255'];
        }

        $this->validate($rules, [
            'body.required' => 'متن نظر را وارد کنید.',
            'authorName.required' => 'نام را وارد کنید.',
            'authorEmail.required' => 'ایمیل را وارد کنید.',
            'authorEmail.email' => 'ایمیل معتبر نیست.',
        ]);

        $key = 'blog-comment:' . request()->ip() . ':' . $this->post->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('body', 'تعداد درخواست‌ها زیاد است. چند دقیقه بعد دوباره تلاش کنید.');

            return;
        }

        RateLimiter::hit($key, 300);

        if ($this->parentId) {
            $parentExists = BlogComment::query()
                ->where('blog_post_id', $this->post->id)
                ->whereKey($this->parentId)
                ->exists();

            if (! $parentExists) {
                $this->parentId = null;
            }
        }

        BlogComment::query()->create([
            'blog_post_id' => $this->post->id,
            'parent_id' => $this->parentId,
            'user_id' => Auth::id(),
            'author_name' => Auth::user()?->name ?? $this->authorName,
            'author_email' => Auth::user()?->email ?? $this->authorEmail,
            'author_ip' => request()->ip(),
            'body' => $this->body,
            'status' => BlogCommentStatus::Pending,
        ]);

        $this->reset('body', 'parentId');
        $this->statusMessage = 'نظر شما ثبت شد و پس از تأیید مدیر نمایش داده می‌شود.';
    }

    public function render()
    {
        $comments = $this->post
            ->approvedRootComments()
            ->with(['children' => fn ($q) => $q->approved()->orderBy('created_at')])
            ->get();

        return view('livewire.shop.blog-post-comments', [
            'comments' => $comments,
        ]);
    }
}