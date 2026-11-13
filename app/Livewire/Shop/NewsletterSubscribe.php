<?php

namespace App\Livewire\Shop;

use App\Models\NewsletterSubscriber;
use Livewire\Component;

class NewsletterSubscribe extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    public function subscribe(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
        ]);

        NewsletterSubscriber::query()->firstOrCreate(
            ['email' => $this->email],
            ['subscribed_at' => now()],
        );

        $this->reset('email');
        $this->statusMessage = 'عضویت شما در خبرنامه ثبت شد.';
    }

    public function render()
    {
        return view('livewire.shop.newsletter-subscribe');
    }
}
