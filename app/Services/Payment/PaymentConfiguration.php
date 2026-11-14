<?php

namespace App\Services\Payment;

use App\Models\PaymentGatewaySetting;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Schema;

class PaymentConfiguration
{
    public function mode(): string
    {
        if ($this->tablesReady()) {
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

        if (! $this->tablesReady()) {
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
        if (! $this->tablesReady()) {
            return true;
        }

        return PaymentGatewaySetting::query()
            ->where('slug', $slug)
            ->value('is_enabled') ?? true;
    }

    protected function tablesReady(): bool
    {
        return Schema::hasTable('payment_settings')
            && Schema::hasTable('payment_gateway_settings');
    }
}
