<?php

namespace App\Models;

use App\Enums\BlogPostStatus;
use App\Support\BlogHtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    protected $fillable = [
        'blog_category_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'featured_image_path',
        'body',
        'status',
        'meta_title',
        'meta_description',
        'tags',
        'allow_comments',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => BlogPostStatus::class,
            'allow_comments' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post): void {
            $post->is_published = $post->isVisibleOnSite();

            if ($post->status === BlogPostStatus::Published && $post->published_at === null) {
                $post->published_at = now();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function approvedRootComments(): HasMany
    {
        return $this->comments()
            ->approved()
            ->whereNull('parent_id')
            ->orderBy('created_at');
    }

    public function scopePublishedOnSite(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->where('status', BlogPostStatus::Published->value)
                ->where(function (Builder $query): void {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                });
        })->orWhere(function (Builder $query): void {
            $query->where('status', BlogPostStatus::Scheduled->value)
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        });
    }

    public function isVisibleOnSite(): bool
    {
        if ($this->status === BlogPostStatus::Draft) {
            return false;
        }

        if ($this->status === BlogPostStatus::Published) {
            return $this->published_at === null || $this->published_at->lte(now());
        }

        if ($this->status === BlogPostStatus::Scheduled) {
            return $this->published_at !== null && $this->published_at->lte(now());
        }

        return false;
    }

    public function featuredImageUrl(): ?string
    {
        if (blank($this->featured_image_path)) {
            return null;
        }

        if (str_starts_with($this->featured_image_path, 'http')) {
            return $this->featured_image_path;
        }

        return Storage::disk('public')->url($this->featured_image_path);
    }

    public function renderedBody(): string
    {
        return app(BlogHtmlSanitizer::class)->clean($this->body);
    }
}