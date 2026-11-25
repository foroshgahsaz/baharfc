<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ProductReviews extends Component
{
    public Product $product;

    public string $authorName = '';

    public string $authorEmail = '';

    public int $rating = 5;

    public string $body = '';

    public ?string $statusMessage = null;

    public function mount(Product $product): void
    {
        $this->product = $product;

        if (Auth::check()) {
            $this->authorName = Auth::user()->name ?? '';
            $this->authorEmail = Auth::user()->email ?? '';
        }
    }

    public function submit(): void
    {
        $rules = [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'min:3', 'max:5000'],
        ];

        if (! Auth::check()) {
            $rules['authorName'] = ['required', 'string', 'max:120'];
            $rules['authorEmail'] = ['required', 'email', 'max:255'];
        }

        $this->validate($rules, [
            'body.required' => 'متن دیدگاه را وارد کنید.',
            'authorName.required' => 'نام را وارد کنید.',
            'authorEmail.required' => 'ایمیل را وارد کنید.',
        ]);

        $key = 'product-review:'.request()->ip().':'.$this->product->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('body', 'تعداد درخواست‌ها زیاد است. چند دقیقه بعد دوباره تلاش کنید.');

            return;
        }

        RateLimiter::hit($key, 300);

        ProductReview::query()->create([
            'product_id' => $this->product->id,
            'user_id' => Auth::id(),
            'author_name' => Auth::user()?->name ?? $this->authorName,
            'author_email' => Auth::user()?->email ?? $this->authorEmail,
            'author_ip' => request()->ip(),
            'rating' => $this->rating,
            'body' => $this->body,
            'status' => 'pending',
        ]);

        $this->reset('body');
        $this->rating = 5;
        $this->statusMessage = 'دیدگاه شما ثبت شد و پس از تأیید نمایش داده می‌شود.';
        $this->dispatch('product-ugc-updated');
    }

    public function render()
    {
        $reviews = $this->product->approvedReviews()->latest()->get();
        $count = $reviews->count();
        $average = $count > 0 ? round($reviews->avg('rating'), 1) : 0;

        return view('livewire.shop.product-reviews', [
            'reviews' => $reviews,
            'count' => $count,
            'average' => $average,
        ]);
    }
}
