<?php

namespace App\Contracts;

use App\Models\Payment;
use App\Support\Payments\GatewayInitResult;
use App\Support\Payments\GatewayVerifyResult;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function slug(): string;

    public function name(): string;

    public function isConfigured(): bool;

    public function initiate(Payment $payment): GatewayInitResult;

    public function verify(Request $request, Payment $payment): GatewayVerifyResult;
}
