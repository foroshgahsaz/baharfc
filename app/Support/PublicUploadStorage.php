<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PublicUploadStorage
{
    /**
     * زیرپوشه‌هایی که Filament روی دیسک public آپلود می‌کند.
     *
     * @var list<string>
     */
    public static function uploadSubdirectories(): array
    {
        return [
            'shop',
            'banners',
            'categories',
            'products',
            'products/gallery',
            'brands',
        ];
    }

    public static function publicRoot(): string
    {
        return (string) config('filesystems.disks.public.root');
    }

    /**
     * @return array{ok: bool, root: string, message: string}
     */
    public static function ensureUploadDirectories(): array
    {
        $root = self::publicRoot();

        if ($root === '') {
            return [
                'ok' => false,
                'root' => $root,
                'message' => 'مسیر دیسک public خالی است.',
            ];
        }

        if (! File::isDirectory($root)) {
            try {
                File::makeDirectory($root, 0775, true);
            } catch (\Throwable $e) {
                return [
                    'ok' => false,
                    'root' => $root,
                    'message' => "ساخت پوشهٔ ریشه ممکن نیست: {$e->getMessage()}",
                ];
            }
        }

        foreach (self::uploadSubdirectories() as $directory) {
            $path = $root.'/'.str_replace('/', DIRECTORY_SEPARATOR, $directory);

            if (File::isDirectory($path)) {
                continue;
            }

            try {
                File::makeDirectory($path, 0775, true);
            } catch (\Throwable $e) {
                return [
                    'ok' => false,
                    'root' => $root,
                    'message' => "ساخت «{$directory}» ممکن نیست (مجوز نوشتن روی {$root}). {$e->getMessage()}",
                ];
            }
        }

        $probe = 'shop/.write_probe_'.uniqid('', true);
        try {
            if (! Storage::disk('public')->put($probe, 'ok')) {
                return [
                    'ok' => false,
                    'root' => $root,
                    'message' => "دیسک public روی «{$root}» قابل نوشتن نیست (Storage::put شکست خورد).",
                ];
            }
            Storage::disk('public')->delete($probe);
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'root' => $root,
                'message' => "تست نوشتن روی دیسک public ناموفق: {$e->getMessage()}",
            ];
        }

        return [
            'ok' => true,
            'root' => $root,
            'message' => 'پوشه‌های آپلود آماده و قابل نوشتن هستند.',
        ];
    }

    public static function publicSymlinkPath(): string
    {
        return public_path('storage');
    }

    public static function configuredSymlinkTarget(): string
    {
        $links = config('filesystems.links', []);

        return (string) ($links[self::publicSymlinkPath()] ?? self::publicRoot());
    }

    /**
     * @return array{ok: bool, link: string, expected: string, actual: ?string, message: string}
     */
    public static function symlinkDiagnostics(): array
    {
        $link = self::publicSymlinkPath();
        $expected = self::configuredSymlinkTarget();

        if (! file_exists($link)) {
            return [
                'ok' => false,
                'link' => $link,
                'expected' => $expected,
                'actual' => null,
                'message' => 'لینک public/storage وجود ندارد؛ تصاویر /storage/... همیشه ۴۰۴ می‌شوند.',
            ];
        }

        if (! is_link($link)) {
            return [
                'ok' => false,
                'link' => $link,
                'expected' => $expected,
                'actual' => null,
                'message' => 'public/storage پوشهٔ معمولی است نه symlink — باید به دیسک public اشاره کند.',
            ];
        }

        $resolvedLink = realpath($link) ?: null;
        $resolvedExpected = realpath($expected) ?: $expected;

        if ($resolvedLink === null) {
            return [
                'ok' => false,
                'link' => $link,
                'expected' => $expected,
                'actual' => readlink($link) ?: null,
                'message' => 'symlink public/storage شکسته است (target وجود ندارد).',
            ];
        }

        if ($resolvedLink !== $resolvedExpected && ! str_starts_with($resolvedLink, rtrim($resolvedExpected, '/'))) {
            return [
                'ok' => false,
                'link' => $link,
                'expected' => $resolvedExpected,
                'actual' => $resolvedLink,
                'message' => "public/storage به «{$resolvedLink}» می‌رود؛ باید به «{$resolvedExpected}» (FILESYSTEM_PUBLIC_ROOT) برود.",
            ];
        }

        return [
            'ok' => true,
            'link' => $link,
            'expected' => $resolvedExpected,
            'actual' => $resolvedLink,
            'message' => 'symlink public/storage درست است.',
        ];
    }

    public static function k8sFixHint(string $root): string
    {
        return <<<TEXT
یک‌بار داخل پاد (به‌عنوان root یا با volume قابل chown):

  mkdir -p {$root}/shop {$root}/banners {$root}/categories {$root}/products/gallery {$root}/brands
  chown -R www-data:www-data /data/baharfc
  chmod -R ug+rwx /data/baharfc/storage
  php artisan storage:link --force
  php artisan config:clear

در .env مقدار FILESYSTEM_PUBLIC_ROOT باید دقیقاً همین mount باشد:
  FILESYSTEM_PUBLIC_ROOT={$root}
TEXT;
    }
}