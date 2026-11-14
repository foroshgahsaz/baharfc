<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Support\Payments\GatewayInitResult;
use App\Support\Payments\GatewayVerifyResult;
use Illuminate\Http\Request;

class MellatGateway extends AbstractSandboxGateway
{
    public function slug(): string
    {
        return 'mellat';
    }

    public function name(): string
    {
        return 'بانک ملت (به‌پرداخت)';
    }

    public function isConfigured(): bool
    {
        $config = config('payment.gateways.mellat');

        return filled($config['terminal_id'] ?? null)
            && filled($config['username'] ?? null)
            && filled($config['password'] ?? null);
    }

    protected function initiateLive(Payment $payment): GatewayInitResult
    {
        return new GatewayInitResult(
            redirectUrl: $this->signedCallbackUrl($payment, 'success'),
            trackingCode: 'MELLAT-'.$payment->reference,
            meta: ['note' => 'Live Mellat SOAP integration placeholder'],
        );
    }

    protected function verifyLive(Request $request, Payment $payment): GatewayVerifyResult
    {
        return new GatewayVerifyResult(
            successful: true,
            trackingCode: $request->string('RefId')->toString() ?: 'MELLAT-'.$payment->reference,
        );
    }
}
