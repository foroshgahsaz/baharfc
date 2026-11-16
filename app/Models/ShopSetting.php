<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ShopSetting extends Model
{
    protected $fillable = [
        'site_name',
        'logo_wordmark',
        'logo_path',
        'site_description',
        'meta_description',
        'accent_color',
        'maintenance_enabled',
        'maintenance_message',
        'contact_phone',
        'contact_email',
        'footer_text',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('shop_settings')) {
            $model = new self;
            $model->exists = false;

            return $model;
        }

        return Cache::remember('shop_settings.singleton', 300, fn () => static::query()->firstOrCreate([]));
    }

    public static function clearCache(): void
    {
        Cache::forget('shop_settings.singleton');
    }

    public function logoUrl(): ?string
    {
        if (blank($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function displaySiteName(): string
    {
        return filled($this->site_name) ? $this->site_name : (string) config('app.name');
    }

    public function displayWordmark(): string
    {
        return filled($this->logo_wordmark)
            ? $this->logo_wordmark
            : (string) config('shop.logo_wordmark', 'LEBASINO');
    }

    public function displayAccent(): string
    {
        return filled($this->accent_color)
            ? $this->accent_color
            : (string) config('shop.accent', '#462766');
    }
}