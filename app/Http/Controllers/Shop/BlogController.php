<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->publishedOnSite()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->paginate(10);

        return view('shop.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->publishedOnSite()
            ->with(['category', 'author'])
            ->firstOrFail();

        return view('shop.blog.show', compact('post'));
    }
}