# بهار — فروشگاه اینترنتی

**Stack:** Laravel 13 · Filament 5 · Blade · Tailwind CSS 4 · Livewire

## راه‌اندازی

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm ci && npm run build
php artisan serve
```

- **فروشگاه:** http://127.0.0.1:8000
- **ادمین (فارسی RTL):** http://127.0.0.1:8000/admin

### ادمین (پس از seed)

| | |
|---|---|
| ایمیل | `admin@baharfc.local` |
| رمز | `password` |

### مشتری

- **OTP:** `/login` — کد در محیط local داخل `storage/logs/laravel.log`
- **نام کاربری:** `/register` سپس `/login`

فونت **Abar Mid FaNum** در `public/fonts/AbarMidFaNum-Regular.woff2`.
