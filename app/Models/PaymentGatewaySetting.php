<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use JsonException;

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
        ];
    }

    protected function credentials(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): array {
                if (blank($value)) {
                    return [];
                }

                try {
                    $decoded = json_decode(Crypt::decryptString($value), true, 512, JSON_THROW_ON_ERROR);

                    return is_array($decoded) ? $decoded : [];
                } catch (DecryptException|JsonException) {
                    return [];
                }
            },
            set: function (?array $value): ?string {
                if ($value === null || $value === []) {
                    return null;
                }

                return Crypt::encryptString(json_encode($value, JSON_THROW_ON_ERROR));
            },
        );
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
