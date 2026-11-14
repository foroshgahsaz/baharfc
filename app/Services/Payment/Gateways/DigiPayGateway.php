<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Support\Payments\GatewayInitResult;
use App\Support\Payments\GatewayVerifyResult;
use Illuminate\Http\Request;

class DigiPayGateway extends AbstractSandboxGateway
{
    public function slug(): string
    {
        return 'digipay';
    }

    public function name(): string
    {
        return 'دیجی‌پی';
    }

    public function isConfigured(): bool
    {
        $config = config('payment.gateways.digipay');

        return filled($config['client_id'] ?? null) && filled($config['client_secret'] ?? null);
    }

    protected function initiateLive(Payment $payment): GatewayInitResult
    {
        return new GatewayInitResult(
            redirectUrl: $this->signedCallbackUrl($payment, 'success'),
            trackingCode: 'DIGIPAY-'.$payment->reference,
            meta: ['provider' => 'digipay'],
        );
    }

    protected function verifyLive(Request $request, Payment $payment): GatewayVerifyResult
    {
        return new GatewayVerifyResult(
            successful: true,
            trackingCode: $request->string('trackingCode')->toString() ?: 'DIGIPAY-'.$payment->reference,
            meta: ['provider' => 'digipay'],
        );
    }
}
