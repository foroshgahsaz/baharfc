<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'file_name',
        'mime_type',
        'size',
        'width',
        'height',
        'title',
        'alt',
        'caption',
        'description',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function isImage(): bool
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    public function typeLabel(): string
    {
        return match (true) {
            Str::startsWith($this->mime_type, 'image/') => 'تصویر',
            Str::startsWith($this->mime_type, 'video/') => 'ویدیو',
            Str::startsWith($this->mime_type, 'audio/') => 'صوت',
            $this->mime_type === 'application/pdf' => 'PDF',
            default => 'فایل',
        };
    }

    public function formattedSize(): string
    {
        $bytes = (int) $this->size;
        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}