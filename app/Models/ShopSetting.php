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
        'contact_form_enabled',
        'contact_form_title',
        'contact_form_intro',
        'contact_form_success_message',
        'contact_form_show_phone',
        'contact_form_require_phone',
        'contact_form_show_subject',
        'contact_form_require_subject',
        'footer_text',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_enabled' => 'boolean',
            'contact_form_enabled' => 'boolean',
            'contact_form_show_phone' => 'boolean',
            'contact_form_require_phone' => 'boolean',
            'contact_form_show_subject' => 'boolean',
            'contact_form_require_subject' => 'boolean',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('shop_settings')) {
            $model = new self;
            $model->exists = false;

            return $model;
        }

        // Do not cache the Eloquent model — serialized models break after deploy (Incomplete_Class).
        return static::query()->firstOrCreate([]);
    }

    public static function clearCache(): void
    {
        Cache::forget('shop_settings.singleton');
        Cache::forget('shop_settings.id');
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
            : (string) config('shop.logo_wordmark', 'BAHARFC');
    }

    public function displayAccent(): string
    {
        return filled($this->accent_color)
            ? $this->accent_color
            : (string) config('shop.accent', '#462766');
    }
}