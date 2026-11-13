<?php

namespace App\Services;

use App\Contracts\SmsSender;
use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public function __construct(
        private readonly SmsSender $sms,
    ) {}

    public function send(string $rawPhone): string
    {
        $phone = PhoneNumber::normalize($rawPhone);

        if ($phone === null) {
            throw ValidationException::withMessages([
                'phone' => 'شماره موبایل معتبر نیست.',
            ]);
        }

        $cooldownKey = "otp:cooldown:{$phone}";

        if (Cache::has($cooldownKey)) {
            throw ValidationException::withMessages([
                'phone' => 'لطفاً چند ثانیه دیگر دوباره تلاش کنید.',
            ]);
        }

        $length = (int) config('shop.otp.length', 5);
        $code = (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);

        $expires = (int) config('shop.otp.expires_in_seconds', 120);
        $cooldown = (int) config('shop.otp.resend_cooldown_seconds', 60);

        Cache::put(
            $this->cacheKey($phone),
            [
                'hash' => Hash::make($code),
                'attempts' => 0,
            ],
            $expires,
        );

        Cache::put($cooldownKey, true, $cooldown);

        $this->sms->send(
            $phone,
            "کد ورود فروشگاه: {$code}\nاین کد ".(int) ($expires / 60).' دقیقه اعتبار دارد.',
        );

        return $phone;
    }

    public function verify(string $rawPhone, string $code): string
    {
        $phone = PhoneNumber::normalize($rawPhone);

        if ($phone === null) {
            throw ValidationException::withMessages([
                'phone' => 'شماره موبایل معتبر نیست.',
            ]);
        }

        $key = $this->cacheKey($phone);
        $payload = Cache::get($key);

        if (! is_array($payload)) {
            throw ValidationException::withMessages([
                'code' => 'کد منقضی شده است. دوباره درخواست دهید.',
            ]);
        }

        $attempts = (int) ($payload['attempts'] ?? 0);
        $maxAttempts = (int) config('shop.otp.max_attempts', 5);

        if ($attempts >= $maxAttempts) {
            Cache::forget($key);

            throw ValidationException::withMessages([
                'code' => 'تعداد تلاش بیش از حد مجاز است. کد جدید بگیرید.',
            ]);
        }

        if (! Hash::check(Str::of($code)->trim()->toString(), $payload['hash'])) {
            $payload['attempts'] = $attempts + 1;
            Cache::put($key, $payload, (int) config('shop.otp.expires_in_seconds', 120));

            throw ValidationException::withMessages([
                'code' => 'کد وارد شده صحیح نیست.',
            ]);
        }

        Cache::forget($key);

        return $phone;
    }

    private function cacheKey(string $phone): string
    {
        return "otp:verify:{$phone}";
    }
}
