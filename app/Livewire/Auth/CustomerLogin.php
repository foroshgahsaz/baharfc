<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class CustomerLogin extends Component
{
    public string $mode = 'otp';

    public string $phone = '';

    public string $code = '';

    public bool $otpSent = false;

    public string $username = '';

    public string $password = '';

    public function switchMode(string $mode): void
    {
        if (! in_array($mode, ['otp', 'password'], true)) {
            return;
        }

        $this->mode = $mode;
        $this->resetErrorBag();
    }

    public function sendOtp(OtpService $otpService): void
    {
        $this->validate([
            'phone' => ['required', 'string', 'min:10', 'max:20'],
        ], [
            'phone.required' => 'شماره موبایل را وارد کنید.',
        ]);

        $normalized = $otpService->send($this->phone);
        $this->phone = $normalized;
        $this->otpSent = true;
        $this->reset('code');
        session()->flash('status', 'کد ورود برای شما ارسال شد.');
    }

    public function verifyOtp(OtpService $otpService): void
    {
        $this->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string', 'min:4', 'max:8'],
        ], [
            'code.required' => 'کد تأیید را وارد کنید.',
        ]);

        $phone = $otpService->verify($this->phone, $this->code);

        $user = User::query()->where('phone', $phone)->first();

        if ($user === null) {
            $user = User::query()->create([
                'name' => 'مشتری '.substr($phone, -4),
                'phone' => $phone,
                'phone_verified_at' => now(),
                'password' => Str::password(32),
            ]);
        } else {
            $user->forceFill(['phone_verified_at' => now()])->save();
        }

        Auth::login($user, remember: true);

        $this->redirectIntended(route('home'), navigate: true);
    }

    public function loginWithPassword(): void
    {
        $this->validate([
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'نام کاربری را وارد کنید.',
            'password.required' => 'رمز عبور را وارد کنید.',
        ]);

        $user = User::query()
            ->where('username', $this->username)
            ->where('is_admin', false)
            ->first();

        if ($user === null || ! $user->hasPassword() || ! Hash::check($this->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => 'نام کاربری یا رمز عبور نادرست است.',
            ]);
        }

        Auth::login($user, remember: true);

        $this->redirectIntended(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.customer-login');
    }
}
