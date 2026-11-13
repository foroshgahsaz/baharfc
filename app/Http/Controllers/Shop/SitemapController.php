<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('products.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('blog.index'), 'changefreq' => 'weekly', 'priority' => '0.6'],
        ]);

        Product::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->each(function (Product $product) use (&$urls): void {
                $urls->push([
                    'loc' => route('products.show', $product->slug),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ]);
            });

        BlogPost::query()
            ->where('is_published', true)
            ->orderBy('id')
            ->each(function (BlogPost $post) use (&$urls): void {
                $urls->push([
                    'loc' => route('blog.show', $post->slug),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ]);
            });

        $xml = view('shop.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
