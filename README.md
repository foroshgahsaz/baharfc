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

## استقرار در سرور

جزئیات کامل در [`DEPLOY.md`](DEPLOY.md) است. خلاصه:

1. محیط تولید: `APP_ENV=production`, `APP_DEBUG=false`, دیتابیس و `APP_URL` صحیح
2. `composer install --no-dev` و `npm run build`
3. `php artisan migrate --force` و کش تنظیمات/مسیر/ویو
4. HTTPS پشت Nginx/Apache با document root برابر `public/`
5. صف (`queue:work`) و cron برای `schedule:run`
6. پس از deploy مسیرهای `/up` و `/sitemap.xml` را بررسی کنید

سرصفحه‌های امنیتی (CSP-pending، HSTS در HTTPS، جلوگیری از clickjacking) از طریق middleware `SecurityHeaders` اعمال می‌شوند. محدودیت نرخ روی ورود/ثبت‌نام و checkout فعال است.

## تست

```bash
php artisan test
```
