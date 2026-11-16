<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'image_path',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function slideImageUrl(): ?string
    {
        if (filled($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return filled($this->image_url) ? $this->image_url : null;
    }
}
