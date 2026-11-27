<?php

namespace App\Support;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class BlogSidebar
{
    public static function data(): array
    {
        if (! Schema::hasTable('blog_posts')) {
            return [
                'sidebarCategories' => collect(),
                'popularTags' => collect(),
                'recentPosts' => collect(),
                'archiveMonths' => collect(),
            ];
        }

        $publishedCount = fn ($query) => $query->publishedOnSite();

        $sidebarCategories = BlogCategory::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['posts as published_posts_count' => $publishedCount])
            ->orderBy('name')
            ->get()
            ->filter(fn (BlogCategory $cat) => $cat->published_posts_count > 0);

        $popularTags = collect();
        if (Schema::hasTable('blog_tags')) {
            $popularTags = BlogTag::query()
                ->withCount(['posts as published_posts_count' => $publishedCount])
                ->having('published_posts_count', '>', 0)
                ->orderByDesc('published_posts_count')
                ->limit(24)
                ->get();
        }

        $recentPosts = BlogPost::query()
            ->publishedOnSite()
            ->latest('published_at')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'published_at']);

        $archiveMonths = BlogPost::query()
            ->publishedOnSite()
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get(['published_at'])
            ->groupBy(fn (BlogPost $post) => $post->published_at->format('Y-n'))
            ->map(function ($group, $key) {
                [$year, $month] = array_map('intval', explode('-', (string) $key, 2));

                return (object) [
                    'year' => $year,
                    'month' => $month,
                    'total' => $group->count(),
                ];
            })
            ->sortByDesc(fn ($row) => sprintf('%04d-%02d', $row->year, $row->month))
            ->values()
            ->take(24);

        return [
            'sidebarCategories' => $sidebarCategories,
            'popularTags' => $popularTags,
            'recentPosts' => $recentPosts,
            'archiveMonths' => $archiveMonths,
        ];
    }
}