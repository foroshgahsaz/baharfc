<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    protected static function booted(): void
    {
        static::creating(function (BlogTag $tag): void {
            if (blank($tag->slug) && filled($tag->name)) {
                $slug = Str::slug($tag->name);
                $tag->slug = $slug !== '' ? $slug : 'tag-'.Str::random(10);
            }
        });
    }
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_blog_tag');
    }

}