<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Response;

class BlogFeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = BlogPost::query()
            ->publishedOnSite()
            ->with(['author', 'category'])
            ->latest('published_at')
            ->limit(20)
            ->get();

        $xml = view('shop.blog.feed', [
            'posts' => $posts,
            'channelTitle' => config('app.name').' — بلاگ',
            'channelUrl' => route('blog.index'),
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}