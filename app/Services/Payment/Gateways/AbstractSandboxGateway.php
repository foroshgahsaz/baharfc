<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;
use App\Support\Payments\GatewayInitResult;
use App\Support\Payments\GatewayVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

abstract class AbstractSandboxGateway implements PaymentGatewayInterface
{
    protected function sandboxMode(): bool
    {
        return config('payment.mode') === 'sandbox' || ! $this->isConfigured();
    }

    protected function signedCallbackUrl(Payment $payment, string $status): string
    {
        return URL::temporarySignedRoute(
            'payment.callback',
            now()->addHour(),
            [
                'gateway' => $this->slug(),
                'payment' => $payment->id,
                'status' => $status,
            ],
        );
    }

    public function initiate(Payment $payment): GatewayInitResult
    {
        if ($this->sandboxMode()) {
            $successUrl = $this->signedCallbackUrl($payment, 'success');
            $tracking = 'SANDBOX-'.strtoupper($this->slug()).'-'.$payment->id;

            return new GatewayInitResult(
                redirectUrl: $successUrl,
                trackingCode: $tracking,
                meta: ['sandbox' => true],
            );
        }

        return $this->initiateLive($payment);
    }

    public function verify(Request $request, Payment $payment): GatewayVerifyResult
    {
        if (! $request->hasValidSignature()) {
            return new GatewayVerifyResult(successful: false, failureReason: 'امضای بازگشت نامعتبر است.');
        }

        if ((int) $request->integer('payment') !== $payment->id) {
            return new GatewayVerifyResult(successful: false, failureReason: 'شناسه پرداخت نامعتبر است.');
        }

        if ($request->string('status')->toString() !== 'success') {
            return new GatewayVerifyResult(successful: false, failureReason: 'پرداخت توسط کاربر لغو شد.');
        }

        if ($this->sandboxMode()) {
            return new GatewayVerifyResult(
                successful: true,
                trackingCode: 'SANDBOX-'.strtoupper($this->slug()).'-'.$payment->id,
                meta: ['sandbox' => true],
            );
        }

        return $this->verifyLive($request, $payment);
    }

    abstract protected function initiateLive(Payment $payment): GatewayInitResult;

    abstract protected function verifyLive(Request $request, Payment $payment): GatewayVerifyResult;
}
