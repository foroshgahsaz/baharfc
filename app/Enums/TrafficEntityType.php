<?php

namespace App\Enums;

enum TrafficEntityType: string
{
    case Product = 'product';
    case BlogPost = 'blog_post';
    case Page = 'page';
    case Category = 'category';

    public function label(): string
    {
        return match ($this) {
            self::Product => 'محصول',
            self::BlogPost => 'مقاله بلاگ',
            self::Page => 'صفحه',
            self::Category => 'دسته (لیست محصول)',
        };
    }
}