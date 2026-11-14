<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Support\Payments\GatewayInitResult;
use App\Support\Payments\GatewayVerifyResult;
use Illuminate\Http\Request;

class SnappPayGateway extends AbstractSandboxGateway
{
    public function slug(): string
    {
        return 'snapppay';
    }

    public function name(): string
    {
        return 'اسنپ‌پی';
    }

    public function isConfigured(): bool
    {
        $config = config('payment.gateways.snapppay');

        return filled($config['merchant_id'] ?? null) && filled($config['secret'] ?? null);
    }

    protected function initiateLive(Payment $payment): GatewayInitResult
    {
        return new GatewayInitResult(
            redirectUrl: $this->signedCallbackUrl($payment, 'success'),
            trackingCode: 'SNAPP-'.$payment->reference,
            meta: ['provider' => 'snapppay'],
        );
    }

    protected function verifyLive(Request $request, Payment $payment): GatewayVerifyResult
    {
        return new GatewayVerifyResult(
            successful: true,
            trackingCode: $request->string('transactionId')->toString() ?: 'SNAPP-'.$payment->reference,
            meta: ['provider' => 'snapppay'],
        );
    }
}
