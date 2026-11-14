<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewaySetting extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'is_enabled',
        'credentials',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'credentials' => 'encrypted:array',
        ];
    }

    public function isConfigured(): bool
    {
        $credentials = $this->credentials ?? [];

        return match ($this->slug) {
            'mellat' => filled($credentials['terminal_id'] ?? null)
                && filled($credentials['username'] ?? null)
                && filled($credentials['password'] ?? null),
            'parsian' => filled($credentials['pin'] ?? null),
            'digipay' => filled($credentials['client_id'] ?? null)
                && filled($credentials['client_secret'] ?? null),
            'snapppay' => filled($credentials['merchant_id'] ?? null)
                && filled($credentials['secret'] ?? null),
            default => false,
        };
    }
}
