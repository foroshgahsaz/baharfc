<?php

namespace App\Http\Controllers\Payment;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function __invoke(Request $request, string $gateway, PaymentService $payments): RedirectResponse
    {
        $payment = Payment::query()->with('order')->findOrFail($request->integer('payment'));

        if ($payment->status === PaymentStatus::Paid) {
            return redirect()->route('checkout.payment', $payment->order)
                ->with('payment_status', 'success');
        }

        try {
            $payments->handleCallback($request, $gateway, $payment);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('checkout.payment', $payment->order)
                ->with('payment_status', 'error')
                ->with('payment_message', $e->getMessage());
        }

        $flash = $payment->fresh()->status === PaymentStatus::Paid ? 'success' : 'error';

        return redirect()->route('checkout.payment', $payment->order)
            ->with('payment_status', $flash);
    }
}
