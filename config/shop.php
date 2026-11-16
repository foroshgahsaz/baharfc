<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shop front wordmark (Lebasino-style header)
    |--------------------------------------------------------------------------
    */
    'logo_wordmark' => env('SHOP_LOGO_WORDMARK', 'BAHARFC'),

    'accent' => env('SHOP_ACCENT_COLOR', '#462766'),

    'demo_category_icons' => [
        ['label' => 'بلوز زنانه', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-1.jpg'],
        ['label' => 'ژاکت مردانه', 'slug' => 'men', 'image' => 'images/lebasino/categories/cat-2.jpg'],
        ['label' => 'کت زنانه', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-3.jpg'],
        ['label' => 'کالکشن گلدان', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-4.jpg'],
        ['label' => 'بلوز زنانه ست', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-5.jpg'],
        ['label' => 'شومیز مردانه', 'slug' => 'men', 'image' => 'images/lebasino/categories/cat-6.jpg'],
        ['label' => 'تونیک مردانه', 'slug' => 'men', 'image' => 'images/lebasino/categories/cat-7.jpg'],
        ['label' => 'ست تونیک دختر', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-8.jpg'],
        ['label' => 'شومیز آستین بلند', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-9.jpg'],
        ['label' => 'تاپ آستین کوتاه', 'slug' => 'women', 'image' => 'images/lebasino/categories/cat-10.jpg'],
    ],

    'nav_links' => [
        ['label' => 'صفحه اصلی', 'route' => 'home', 'active' => 'home'],
        ['label' => 'صفحات قالب', 'route' => 'products.index'],
        ['label' => 'تی شرت', 'route' => 'products.index', 'params' => ['q' => 'تی شرت']],
        ['label' => 'هودی', 'route' => 'products.index', 'params' => ['q' => 'هودی']],
        ['label' => 'کت و پافر', 'route' => 'products.index', 'params' => ['q' => 'کت']],
        ['label' => 'شلوار', 'route' => 'products.index', 'params' => ['q' => 'شلوار']],
        ['label' => 'لباس زیر', 'route' => 'products.index', 'params' => ['q' => 'لباس زیر']],
        ['label' => 'اکسسوری ها', 'route' => 'products.index', 'params' => ['q' => 'اکسسوری']],
    ],

];