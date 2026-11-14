<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewaySetting;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Schema;

class PaymentConfiguration
{
    public function mode(): string
    {
        if ($this->schemaReady()) {
            return PaymentSetting::current()->payment_mode;
        }

        return (string) config('payment.mode', 'sandbox');
    }

    public function sandboxMode(): bool
    {
        return $this->mode() === 'sandbox';
    }

    /**
     * @return array<string, mixed>
     */
    public function gatewayCredentials(string $slug): array
    {
        $defaults = config("payment.gateways.{$slug}", []);

        if (! $this->schemaReady()) {
            return $defaults;
        }

        $row = PaymentGatewaySetting::query()->where('slug', $slug)->first();

        if ($row === null || empty($row->credentials)) {
            return $defaults;
        }

        return array_merge($defaults, array_filter($row->credentials, fn ($v) => filled($v)));
    }

    public function isGatewayEnabled(string $slug): bool
    {
        if (! $this->schemaReady()) {
            return true;
        }

        return PaymentGatewaySetting::query()
            ->where('slug', $slug)
            ->value('is_enabled') ?? true;
    }

    public function schemaReady(): bool
    {
        return Schema::hasTable('payment_settings')
            && Schema::hasTable('payment_gateway_settings');
    }
}
