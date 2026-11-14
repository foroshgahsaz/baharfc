<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Support\Payments\GatewayInitResult;
use App\Support\Payments\GatewayVerifyResult;
use Illuminate\Http\Request;

class ParsianGateway extends AbstractSandboxGateway
{
    public function slug(): string
    {
        return 'parsian';
    }

    public function name(): string
    {
        return 'بانک پارسیان';
    }

    public function isConfigured(): bool
    {
        return filled(config('payment.gateways.parsian.pin'));
    }

    protected function initiateLive(Payment $payment): GatewayInitResult
    {
        return new GatewayInitResult(
            redirectUrl: $this->signedCallbackUrl($payment, 'success'),
            trackingCode: 'PARSIAN-'.$payment->reference,
            meta: ['note' => 'Live Parsian integration placeholder'],
        );
    }

    protected function verifyLive(Request $request, Payment $payment): GatewayVerifyResult
    {
        return new GatewayVerifyResult(
            successful: true,
            trackingCode: $request->string('Token')->toString() ?: 'PARSIAN-'.$payment->reference,
        );
    }
}
