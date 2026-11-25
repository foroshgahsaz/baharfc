<?php

namespace App\Support;

final class AdminPermissions
{
    public const ALL = '*';

    /**
     * @return array<string, array<string, string>>
     */
    public static function groups(): array
    {
        return [
            'عمومی' => [
                'panel.access' => 'ورود به پنل مدیریت',
            ],
            'نقش‌ها و کاربران' => [
                'roles.view' => 'مشاهده نقش‌ها',
                'roles.create' => 'ایجاد نقش',
                'roles.update' => 'ویرایش نقش',
                'roles.delete' => 'حذف نقش',
                'users.view' => 'مشاهده کاربران',
                'users.create' => 'ایجاد کاربر',
                'users.update' => 'ویرایش کاربر',
                'users.delete' => 'حذف کاربر',
            ],
            'کاتالوگ' => [
                'products.view' => 'مشاهده محصولات',
                'products.create' => 'ایجاد محصول',
                'products.update' => 'ویرایش محصول',
                'products.delete' => 'حذف محصول',
                'categories.view' => 'مشاهده دسته‌ها',
                'categories.create' => 'ایجاد دسته',
                'categories.update' => 'ویرایش دسته',
                'categories.delete' => 'حذف دسته',
                'brands.view' => 'مشاهده برندها',
                'brands.create' => 'ایجاد برند',
                'brands.update' => 'ویرایش برند',
                'brands.delete' => 'حذف برند',
                'product_reviews.moderate' => 'مدیریت دیدگاه محصول',
                'product_questions.moderate' => 'مدیریت پرسش محصول',
            ],
            'گزارش‌ها' => [
                'analytics.view' => 'مشاهده آمار بازدید داخلی',
            ],
            'تماس' => [
                'contact_messages.view' => 'مشاهده پیام‌های تماس',
                'contact_messages.update' => 'مدیریت و پاسخ پیام‌ها',
                'contact_messages.delete' => 'حذف پیام تماس',
            ],
            'فروش و سفارش' => [
                'orders.view' => 'مشاهده سفارش‌ها',
                'orders.create' => 'ایجاد سفارش',
                'orders.update' => 'ویرایش سفارش',
                'orders.delete' => 'حذف سفارش',
                'payments.view' => 'مشاهده پرداخت‌ها',
                'payments.update' => 'ویرایش پرداخت‌ها',
                'returns.view' => 'مشاهده مرجوعی‌ها',
                'returns.update' => 'مدیریت مرجوعی‌ها',
                'coupons.view' => 'مشاهده کوپن‌ها',
                'coupons.create' => 'ایجاد کوپن',
                'coupons.update' => 'ویرایش کوپن',
                'coupons.delete' => 'حذف کوپن',
                'shipping.view' => 'مشاهده روش ارسال',
                'shipping.update' => 'ویرایش روش ارسال',
            ],
            'بازاریابی و بلاگ' => [
                'banners.view' => 'مشاهده بنرها',
                'banners.create' => 'ایجاد بنر',
                'banners.update' => 'ویرایش بنر',
                'banners.delete' => 'حذف بنر',
                'blog_posts.view' => 'مشاهده مقالات',
                'blog_posts.create' => 'ایجاد مقاله',
                'blog_posts.update' => 'ویرایش مقاله',
                'blog_posts.delete' => 'حذف مقاله',
                'blog_categories.view' => 'مشاهده دسته بلاگ',
                'blog_categories.create' => 'ایجاد دسته بلاگ',
                'blog_categories.update' => 'ویرایش دسته بلاگ',
                'blog_categories.delete' => 'حذف دسته بلاگ',
                'blog_comments.moderate' => 'مدیریت نظرات بلاگ',
                'menus.view' => 'مشاهده منوها',
                'menus.create' => 'ایجاد منو',
                'menus.update' => 'ویرایش منو',
                'menus.delete' => 'حذف منو',
                'home_sections.view' => 'مشاهده کاروسل صفحه اصلی',
                'home_sections.create' => 'ایجاد کاروسل صفحه اصلی',
                'home_sections.update' => 'ویرایش کاروسل صفحه اصلی',
                'home_sections.delete' => 'حذف کاروسل صفحه اصلی',
            ],
            'رسانه' => [
                'media.view' => 'مشاهده کتابخانه رسانه',
                'media.upload' => 'آپلود فایل',
                'media.delete' => 'حذف فایل رسانه',
            ],
            'تنظیمات' => [
                'settings.shop' => 'تنظیمات عمومی فروشگاه',
                'settings.notifications' => 'تنظیمات اعلان‌ها',
                'payment_gateways.view' => 'مشاهده درگاه پرداخت',
                'payment_gateways.update' => 'تنظیم درگاه پرداخت',
                'system.logs' => 'آرشیو لاگ خطاها',
                'settings.backups' => 'بک‌آپ دیتابیس و فایل‌ها',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function flatOptions(): array
    {
        $options = [];
        foreach (self::groups() as $permissions) {
            foreach ($permissions as $key => $label) {
                $options[$key] = $label;
            }
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    public static function allKeys(): array
    {
        return array_keys(self::flatOptions());
    }

    /**
     * @return array<string, list<string>>
     */
    public static function presetRolePermissions(): array
    {
        return [
            'author' => [
                'panel.access',
                'blog_posts.view',
                'blog_posts.create',
                'blog_posts.update',
                'blog_posts.delete',
                'blog_categories.view',
                'blog_categories.create',
                'blog_categories.update',
                'blog_categories.delete',
                'blog_comments.moderate',
                'media.view',
                'media.upload',
            ],
            'shop_manager' => [
                'panel.access',
                'products.view',
                'products.create',
                'products.update',
                'products.delete',
                'categories.view',
                'categories.create',
                'categories.update',
                'categories.delete',
                'brands.view',
                'brands.create',
                'brands.update',
                'brands.delete',
                'orders.view',
                'orders.create',
                'orders.update',
                'orders.delete',
                'payments.view',
                'payments.update',
                'returns.view',
                'returns.update',
                'coupons.view',
                'coupons.create',
                'coupons.update',
                'coupons.delete',
                'shipping.view',
                'shipping.update',
                'contact_messages.view',
                'contact_messages.update',
                'contact_messages.delete',
                'users.view',
                'media.view',
                'media.upload',
                'analytics.view',
                'home_sections.view',
                'home_sections.create',
                'home_sections.update',
                'home_sections.delete',
                'product_reviews.moderate',
                'product_questions.moderate',
            ],
        ];
    }
}