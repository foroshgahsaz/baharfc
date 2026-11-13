<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\Gateways\MellatGateway;
use App\Services\Payment\Gateways\ParsianGateway;
use App\Support\Payments\GatewayInitResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PaymentService
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $gateways;

    public function __construct()
    {
        $this->gateways = [
            'mellat' => new MellatGateway,
            'parsian' => new ParsianGateway,
        ];
    }

    /**
     * @return array<string, PaymentGatewayInterface>
     */
    public function availableGateways(): array
    {
        return $this->gateways;
    }

    public function gateway(string $slug): PaymentGatewayInterface
    {
        if (! isset($this->gateways[$slug])) {
            throw new InvalidArgumentException('درگاه نامعتبر است.');
        }

        return $this->gateways[$slug];
    }

    public function registerGateway(PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$gateway->slug()] = $gateway;
    }

    public function createPayment(Order $order, string $gatewaySlug): Payment
    {
        if ($order->status !== OrderStatus::PendingPayment) {
            throw new InvalidArgumentException('این سفارش قابل پرداخت نیست.');
        }

        $gateway = $this->gateway($gatewaySlug);

        return Payment::query()->create([
            'order_id' => $order->id,
            'gateway' => $gateway->slug(),
            'status' => PaymentStatus::Pending,
            'amount' => $order->total,
            'reference' => strtoupper(Str::random(12)),
        ]);
    }

    public function start(Payment $payment): GatewayInitResult
    {
        $gateway = $this->gateway($payment->gateway);
        $result = $gateway->initiate($payment);

        $payment->update([
            'status' => PaymentStatus::Redirected,
            'tracking_code' => $result->trackingCode,
            'meta' => array_merge($payment->meta ?? [], $result->meta),
        ]);

        return $result;
    }

    public function handleCallback(Request $request, string $gatewaySlug, Payment $payment): Payment
    {
        if ($payment->gateway !== $gatewaySlug) {
            throw new InvalidArgumentException('درگاه مطابقت ندارد.');
        }

        if ($payment->amount !== $payment->order->total) {
            throw new InvalidArgumentException('مبلغ پرداخت با سفارش همخوانی ندارد.');
        }

        $gateway = $this->gateway($gatewaySlug);
        $verify = $gateway->verify($request, $payment);

        return DB::transaction(function () use ($payment, $verify) {
            $order = $payment->order()->lockForUpdate()->first();

            if ($verify->successful) {
                $payment->update([
                    'status' => PaymentStatus::Paid,
                    'tracking_code' => $verify->trackingCode ?? $payment->tracking_code,
                    'meta' => array_merge($payment->meta ?? [], $verify->meta),
                    'paid_at' => now(),
                ]);

                $order->update([
                    'status' => OrderStatus::Paid,
                    'paid_at' => now(),
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::Failed,
                    'failure_reason' => $verify->failureReason,
                    'meta' => array_merge($payment->meta ?? [], $verify->meta),
                ]);

                if ($order->status === OrderStatus::PendingPayment) {
                    $order->update(['status' => OrderStatus::Failed]);
                }
            }

            return $payment->fresh();
        });
    }
}
