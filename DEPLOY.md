# چک‌لیست استقرار فروشگاه بهار

## پیش‌نیاز سرور

- PHP 8.3+ با افزونه‌های `pdo`, `mbstring`, `openssl`, `curl`, `gd` یا `imagick` (برای PDF)
- Composer 2.x، Node.js 20+ (برای build فرانت)
- پایگاه داده MySQL/MariaDB یا PostgreSQL (SQLite فقط توسعه)
- Redis یا Memcached (اختیاری برای cache و صف)

## مراحل استقرار

1. کلون مخزن و checkout شاخه release مورد نظر
2. `composer install --no-dev --optimize-autoloader`
3. `cp .env.example .env` و تنظیم `APP_KEY`, `APP_URL`, دیتابیس، درگاه‌های پرداخت
4. `php artisan migrate --force`
5. `npm ci && npm run build`
6. `php artisan vendor:publish --tag=livewire:assets --force` و `php artisan filament:assets`
7. پس از تنظیم/تغییر `APP_KEY`: حتماً `php artisan optimize:clear` (کش view/route قدیمی باعث مسیر اشتباه Livewire و لاگین شکسته می‌شود)
8. `php artisan config:cache` و `php artisan view:cache` (اختیاری؛ بعد از هر تغییر `APP_KEY` دوباره پاک و در صورت نیاز بسازید)
9. تنظیم وب‌سرور (Nginx/Apache) با `public/` به عنوان document root
10. اجرای worker صف: `php artisan queue:work` (systemd/supervisor)
11. کرون لاراول: `* * * * * php /path/to/artisan schedule:run`

## ورود ادمین (پس از سیدر)

- آدرس: `/admin/login`
- **ایمیل:** `admin@baharfc.local` (فیلامنت با ایمیل لاگین می‌کند، نه نام کاربری `admin`)
- رمز: `password` — حتماً بعد از اولین ورود عوض کنید

## امنیت و عملکرد

- HTTPS اجباری و HSTS در reverse proxy؛ `APP_URL=https://example.com` (بدون `/` آخر). اپ پشت load balancer/Kubernetes با `trustProxies` لینک CSS/JS را با `https` می‌سازد — در غیر این صورت مرورگر استایل را block می‌کند (Mixed Content).
- `APP_DEBUG=false` در تولید
- بک‌آپ دوره‌ای دیتابیس و فایل‌های `storage/app/public`
- مانیتور لاگ (`storage/logs`) و health check مسیر `/up`
- بررسی sitemap در `/sitemap.xml` پس از deploy

## فاز ۱۳ — API پست (Post API)

> **توسعه آینده:** فاز ۱۳ (REST API مدیریت محتوا/پست) عمداً از این موج عقب افتاده است. پس از merge فاز ۱۴، endpointهای عمومی را در شاخه جداگانه اضافه کنید.
