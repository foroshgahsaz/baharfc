<?php

namespace App\Livewire\Shop;

use App\Models\Product;
use App\Models\ProductQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ProductQuestions extends Component
{
    public Product $product;

    public string $authorName = '';

    public string $authorEmail = '';

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
            'body' => ['required', 'string', 'min:3', 'max:3000'],
        ];

        if (! Auth::check()) {
            $rules['authorName'] = ['required', 'string', 'max:120'];
            $rules['authorEmail'] = ['required', 'email', 'max:255'];
        }

        $this->validate($rules, [
            'body.required' => 'متن پرسش را وارد کنید.',
            'authorName.required' => 'نام را وارد کنید.',
            'authorEmail.required' => 'ایمیل را وارد کنید.',
        ]);

        $key = 'product-question:'.request()->ip().':'.$this->product->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('body', 'تعداد درخواست‌ها زیاد است. چند دقیقه بعد دوباره تلاش کنید.');

            return;
        }

        RateLimiter::hit($key, 300);

        ProductQuestion::query()->create([
            'product_id' => $this->product->id,
            'user_id' => Auth::id(),
            'author_name' => Auth::user()?->name ?? $this->authorName,
            'author_email' => Auth::user()?->email ?? $this->authorEmail,
            'author_ip' => request()->ip(),
            'body' => $this->body,
            'status' => 'pending',
        ]);

        $this->reset('body');
        $this->statusMessage = 'پرسش شما ثبت شد و پس از تأیید نمایش داده می‌شود.';
        $this->dispatch('product-ugc-updated');
    }

    public function render()
    {
        $questions = $this->product
            ->approvedQuestions()
            ->with(['approvedAnswers'])
            ->latest()
            ->get();

        return view('livewire.shop.product-questions', [
            'questions' => $questions,
            'count' => $questions->count(),
        ]);
    }
}
