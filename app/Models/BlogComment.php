<?php

namespace App\Models;

use App\Enums\BlogCommentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    protected $fillable = [
        'blog_post_id',
        'parent_id',
        'user_id',
        'author_name',
        'author_email',
        'author_ip',
        'body',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => BlogCommentStatus::class,
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', BlogCommentStatus::Approved);
    }

    public function scopeModeratable(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            BlogCommentStatus::Trash->value,
            BlogCommentStatus::Spam->value,
        ]);
    }
}