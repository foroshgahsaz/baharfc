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
4. **یک‌خطی پیشنهادی بعد از هر `git pull` / rollout پاد جدید:** `php artisan baharfc:post-deploy` (migrate + تعمیر symlink + پاک‌سازی کش؛ با `--seed-menu` در صورت نیاز منو)
   - از **فقط** `migrate` بدون `post-deploy` خودداری کنید — symlink `public/storage` معمولاً بعد از deploy می‌پاشد.
   - از نسخهٔ جدید: اپلیکیشن در **اولین درخواست وب** هم symlink را خودکار درست می‌کند (اگر `FILESYSTEM_PUBLIC_ROOT` درست باشد).
   - یا دستی: `php artisan migrate --force` (شامل `show_on_home` / `image_path` بنرها پس از به‌روزرسانی صفحه اصلی)
5. **`git pull` باید پوشه `public/build/` را هم بیاورد** یا روی سرور `npm ci && npm run build` بزنید — اگر Blade به entryای در Vite اشاره کند که در `manifest.json` نیست، **صفحه اصلی ۵۰۰** می‌دهد. برای مودال کتابخانه رسانه استایل در Blade هم هست؛ بعد از pull حتماً `php artisan view:clear` بزنید تا تصاویر بزرگ خام لود نشوند.
6. `php artisan vendor:publish --tag=livewire:assets --force` و `php artisan filament:assets`
6a. **لاگ پایدار (آرشیو خطاها):** در K8s مقدار `APP_LOG_ROOT=/data/baharfc/storage/logs` را در `.env` بگذارید؛ `baharfc:post-deploy` پوشه را می‌سازد و در صورت نیاز `storage/logs` را به آن symlink می‌کند تا بعد از هر deploy لاگ و آرشیو خطا **پاک نشود**.

6a2. **بک‌آپ پایدار:** مسیر جدا از کد (مثل تصاویر). اگر `FILESYSTEM_PUBLIC_ROOT=/data/baharfc/storage/app/public` باشد، پیش‌فرض بک‌آپ `/data/baharfc/storage/backups` است. صریح:

```env
APP_BACKUP_ROOT=/data/baharfc/storage/backups
```

```bash
mkdir -p /data/baharfc/storage/backups
chown -R www-data:www-data /data/baharfc/storage/backups
```

از پنل: **تنظیمات → بک‌آپ**. حداکثر **۴ نسخه** و **۲ روز** نگهداری. کرون: `* * * * * php artisan schedule:run` (بک‌آپ روزانه ساعت ۰۳:۲۰).

6b. **مجوزهای K8s / PHP-FPM:** پوشه `bootstrap/cache` باید وجود داشته باشد و برای کاربر وب‌سرور (مثلاً `www-data`) قابل نوشتن باشد؛ وگرنه در لاگ `The bootstrap/cache directory must be present and writable` و سایت **۵۰۰** می‌دهد (`artisan` به‌صورت root ممکن است سالم بماند):

```bash
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs
chown -R www-data:www-data bootstrap/cache storage
chmod -R ug+rwx bootstrap/cache storage
```
7. پس از هر `git pull` روی ادمین Filament: `php artisan optimize:clear` و `php artisan filament:optimize-clear` (بدون آن صفحاتی مثل درگاه‌های پرداخت ممکن است ۵۰۰ بدهند)
8. پس از تنظیم/تغییر `APP_KEY`: حتماً `php artisan optimize:clear` (کش view/route قدیمی باعث مسیر اشتباه Livewire و لاگین شکسته می‌شود)
9. `php artisan config:cache` و `php artisan view:cache` (اختیاری؛ بعد از هر تغییر `APP_KEY` دوباره پاک و در صورت نیاز بسازید)
10. تنظیم وب‌سرور (Nginx/Apache) با `public/` به عنوان document root
11. اجرای worker صف: `php artisan queue:work` (systemd/supervisor)
12. کرون لاراول: `* * * * * php /path/to/artisan schedule:run`

## ورود ادمین (پس از سیدر)

- آدرس: `/admin/login`
- **ایمیل:** `admin@baharfc.local` (فیلامنت با ایمیل لاگین می‌کند، نه نام کاربری `admin`)
- رمز: `password` — حتماً بعد از اولین ورود عوض کنید

## فایل‌ها و تصاویر (دیسک پایدار)

آپلودهای ادمین روی دیسک **`public`** ذخیره می‌شوند (مسیر پیش‌فرض: `storage/app/public`). با هر deploy اگر کد از نو checkout شود، این پوشه خالی می‌شود؛ **تنها `FILESYSTEM_DISK` کافی نیست** چون فرم‌ها صریحاً `disk('public')` استفاده می‌کنند.

### روش توصیه‌شده: دیسک جدا + متغیر env

1. دیسک پایدار را mount کنید (مثلاً `/data`).
2. یک‌بار پوشه را بسازید و مالکیت وب‌سرور را بدهید:

```bash
sudo mkdir -p /data/baharfc/storage/app/public
sudo chown -R www-data:www-data /data/baharfc
```

3. در `.env` روی سرور:

```env
FILESYSTEM_PUBLIC_ROOT=/data/baharfc/storage/app/public
```

3b. **مجوز نوشتن (خطای `mkdir(): Permission denied` در تنظیمات سایت / آپلود لوگو):** کاربر PHP (معمولاً `www-data`) باید روی `/data/baharfc` مالک باشد. یک‌بار داخل پاد:

```bash
mkdir -p /data/baharfc/storage/app/public/{shop,banners,categories,products/gallery,brands}
chown -R www-data:www-data /data/baharfc
chmod -R ug+rwx /data/baharfc/storage
php artisan baharfc:ensure-storage
php artisan storage:link
```

بدون این مرحله ذخیرهٔ **تنظیمات سایت** با آپلود لوگو در `/admin/shop-settings` شکست می‌خورد.

4. اگر قبلاً فایل داخل پروژه بود، یک‌بار جابه‌جا کنید:

```bash
rsync -a storage/app/public/ /data/baharfc/storage/app/public/
```

5. بعد از هر deploy (یا اگر ۴۰۴ ماند):

```bash
php artisan baharfc:post-deploy
# یا فقط:
php artisan baharfc:repair-storage
php artisan baharfc:ensure-storage
```

**K8s (پایدار):** در Deployment یک `lifecycle.postStart` یا entrypoint که `php artisan baharfc:post-deploy` را بزند تا هر پاد تازه symlink درست داشته باشد.

```yaml
lifecycle:
  postStart:
    exec:
      command: ["/bin/sh", "-c", "cd /var/www && php artisan baharfc:post-deploy || true"]
```

اگر `public/storage` به جای دیسک `/data` باشد، **آپلود در ادمین OK ولی `/storage/...` ۴۰۴** می‌شود.

اگر فقط نام فایل در ادمین دیده می‌شود و پیش‌نمایش نمی‌آید: اول symlink را درست کنید؛ اگر باز ۴۰۴ بود فایل واقعاً روی volume نیست — دوباره آپلود کنید.

6. پس از تغییر `.env`: `php artisan config:clear` یا `php artisan optimize:clear`

### جایگزین: symlink (بدون env)

```bash
rm -rf storage/app/public && ln -sfn /data/baharfc/storage/app/public storage/app/public
php artisan storage:link
```

همچنین برای لاگ و کش: `storage/logs`, `storage/framework`, `bootstrap/cache` را به `/data/...` symlink کنید تا deploy آن‌ها را هم پاک نکند.

### ذخیره ابری (اختیاری)

برای S3/فضای ابری باید `AWS_*` و تغییر دیسک در کد پشتیبانی شود؛ در وضعیت فعلی پروژه مسیر بالا ساده‌تر است.

## امنیت و عملکرد

- HTTPS اجباری و HSTS در reverse proxy؛ `APP_URL=https://example.com` (بدون `/` آخر). اپ پشت load balancer/Kubernetes با `trustProxies` لینک CSS/JS را با `https` می‌سازد — در غیر این صورت مرورگر استایل را block می‌کند (Mixed Content).
- `APP_DEBUG=false` در تولید
- بک‌آپ دوره‌ای دیتابیس و فایل‌های `storage/app/public`
- مانیتور لاگ (`storage/logs`) و health check مسیر `/up`
- بررسی sitemap در `/sitemap.xml` پس از deploy

## فاز ۱۳ — API پست (Post API)

> **توسعه آینده:** فاز ۱۳ (REST API مدیریت محتوا/پست) عمداً از این موج عقب افتاده است. پس از merge فاز ۱۴، endpointهای عمومی را در شاخه جداگانه اضافه کنید.
