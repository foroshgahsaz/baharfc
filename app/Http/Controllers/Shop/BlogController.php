<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use App\Support\BlogSidebar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->basePostQuery();

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $posts = $query->latest('published_at')->paginate(12)->withQueryString();

        $featuredPosts = BlogPost::query()
            ->publishedOnSite()
            ->where('is_featured', true)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('shop.blog.index', array_merge(
            BlogSidebar::data(),
            [
                'posts' => $posts,
                'featuredPosts' => $featuredPosts,
                'title' => 'بلاگ',
                'pageMetaDescription' => 'مقالات، راهنماها و اخبار فروشگاه',
                'archiveHeading' => 'آخرین مطالب',
                'archiveIntro' => null,
            ],
        ));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->publishedOnSite()
            ->with(['category', 'author', 'tags'])
            ->firstOrFail();

        $related = BlogPost::query()
            ->publishedOnSite()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn (Builder $q) => $q->where('blog_category_id', $post->blog_category_id))
            ->latest('published_at')
            ->limit(4)
            ->get();

        if ($related->count() < 3) {
            $related = BlogPost::query()
                ->publishedOnSite()
                ->where('id', '!=', $post->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->latest('published_at')
                ->limit(4)
                ->get()
                ->concat($related)
                ->unique('id')
                ->take(4);
        }

        return view('shop.blog.show', array_merge(BlogSidebar::data(), [
            'post' => $post,
            'related' => $related,
            'title' => $post->meta_title ?: $post->title,
            'pageMetaDescription' => $post->metaDescriptionForSeo(),
        ]));
    }

    public function category(string $slug): View
    {
        $category = BlogCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = $this->basePostQuery()
            ->where('blog_category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        return view('shop.blog.archive', array_merge(BlogSidebar::data(), [
            'posts' => $posts,
            'title' => $category->meta_title ?: 'دسته: '.$category->name,
            'pageMetaDescription' => $category->meta_description ?: $category->description,
            'archiveHeading' => $category->name,
            'archiveIntro' => $category->description,
        ]));
    }

    public function tag(string $slug): View
    {
        $tag = BlogTag::query()->where('slug', $slug)->firstOrFail();

        $posts = $this->basePostQuery()
            ->whereHas('tags', fn (Builder $q) => $q->where('blog_tags.id', $tag->id))
            ->latest('published_at')
            ->paginate(12);

        return view('shop.blog.archive', array_merge(BlogSidebar::data(), [
            'posts' => $posts,
            'tag' => $tag,
            'title' => 'برچسب: '.$tag->name,
            'pageMetaDescription' => $tag->description,
            'archiveHeading' => 'برچسب: '.$tag->name,
            'archiveIntro' => $tag->description,
        ]));
    }

    public function author(User $author): View
    {
        $posts = $this->basePostQuery()
            ->where('author_id', $author->id)
            ->latest('published_at')
            ->paginate(12);

        return view('shop.blog.archive', array_merge(BlogSidebar::data(), [
            'posts' => $posts,
            'author' => $author,
            'title' => 'نویسنده: '.$author->name,
            'pageMetaDescription' => 'مقالات نوشته‌شده توسط '.$author->name,
            'archiveHeading' => 'مقالات '.$author->name,
            'archiveIntro' => null,
        ]));
    }

    public function archive(int $year, ?int $month = null): View
    {
        abort_unless($year >= 2000 && $year <= (int) now()->format('Y') + 1, 404);
        if ($month !== null) {
            abort_unless($month >= 1 && $month <= 12, 404);
        }

        $query = $this->basePostQuery()
            ->whereYear('published_at', $year);

        if ($month !== null) {
            $query->whereMonth('published_at', $month);
        }

        $posts = $query->latest('published_at')->paginate(12);

        $heading = $month
            ? sprintf('آرشیو %d / %02d', $year, $month)
            : "آرشیو سال {$year}";

        return view('shop.blog.archive', array_merge(BlogSidebar::data(), [
            'posts' => $posts,
            'title' => $heading,
            'pageMetaDescription' => $heading,
            'archiveHeading' => $heading,
            'archiveIntro' => null,
            'archiveYear' => $year,
            'archiveMonth' => $month,
        ]));
    }

    public function search(Request $request): View
    {
        $q = trim((string) $request->string('q'));

        $posts = $this->basePostQuery()
            ->when($q !== '', function (Builder $query) use ($q): void {
                $query->where(function (Builder $inner) use ($q): void {
                    $inner->where('title', 'like', '%'.$q.'%')
                        ->orWhere('excerpt', 'like', '%'.$q.'%')
                        ->orWhere('body', 'like', '%'.$q.'%');
                });
            })
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('shop.blog.archive', array_merge(BlogSidebar::data(), [
            'posts' => $posts,
            'searchQuery' => $q,
            'title' => $q !== '' ? "جستجو: {$q}" : 'جستجو در بلاگ',
            'pageMetaDescription' => 'نتایج جستجو در بلاگ',
            'archiveHeading' => $q !== '' ? "نتایج جستجو برای «{$q}»" : 'جستجو در بلاگ',
            'archiveIntro' => null,
        ]));
    }

    private function basePostQuery(): Builder
    {
        return BlogPost::query()
            ->publishedOnSite()
            ->with(['category', 'author', 'tags']);
    }
}