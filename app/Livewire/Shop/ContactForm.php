<?php

namespace App\Livewire\Shop;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use App\Models\ShopSetting;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public ?string $statusMessage = null;

    public function rules(): array
    {
        $settings = ShopSetting::current();

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];

        if ($settings->contact_form_show_phone) {
            $phoneRules = ['nullable', 'string', 'max:30'];
            if ($settings->contact_form_require_phone) {
                $phoneRules = ['required', 'string', 'max:30'];
            }
            $rules['phone'] = $phoneRules;
        }

        if ($settings->contact_form_show_subject) {
            $subjectRules = ['nullable', 'string', 'max:200'];
            if ($settings->contact_form_require_subject) {
                $subjectRules = ['required', 'string', 'max:200'];
            }
            $rules['subject'] = $subjectRules;
        }

        return $rules;
    }

    public function submit(): void
    {
        $settings = ShopSetting::current();
        if (! $settings->contact_form_enabled) {
            return;
        }

        $this->validate($this->rules(), [
            'name.required' => 'نام را وارد کنید.',
            'email.required' => 'ایمیل را وارد کنید.',
            'email.email' => 'ایمیل معتبر نیست.',
            'phone.required' => 'شماره تماس را وارد کنید.',
            'subject.required' => 'موضوع را وارد کنید.',
            'message.required' => 'متن پیام را وارد کنید.',
            'message.min' => 'پیام خیلی کوتاه است.',
        ]);

        $key = 'contact-form:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', 'تعداد ارسال زیاد است. چند دقیقه بعد دوباره تلاش کنید.');

            return;
        }

        RateLimiter::hit($key, 300);

        ContactMessage::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $settings->contact_form_show_phone ? $this->phone : null,
            'subject' => $settings->contact_form_show_subject ? $this->subject : null,
            'message' => $this->message,
            'status' => ContactMessageStatus::New,
            'ip' => request()->ip(),
        ]);

        $this->reset('name', 'email', 'phone', 'subject', 'message');
        $this->statusMessage = filled($settings->contact_form_success_message)
            ? $settings->contact_form_success_message
            : 'پیام شما ثبت شد. به زودی با شما تماس می‌گیریم.';
    }

    public function render()
    {
        $settings = ShopSetting::current();

        return view('livewire.shop.contact-form', [
            'settings' => $settings,
        ]);
    }
}