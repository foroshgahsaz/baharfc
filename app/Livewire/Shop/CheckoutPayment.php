<?php

namespace App\Livewire\Shop;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class CheckoutPayment extends Component
{
    use AuthorizesRequests;

    public Order $order;

    public string $gateway = 'mellat';

    public function mount(Order $order): void
    {
        $this->authorize('view', $order);
        $this->order = $order->load(['items', 'user']);
    }

    public function pay(PaymentService $payments): void
    {
        if ($this->order->status !== OrderStatus::PendingPayment) {
            return;
        }

        $payment = $payments->createPayment($this->order, $this->gateway);
        $result = $payments->start($payment);

        $this->redirect($result->redirectUrl);
    }

    public function render(PaymentService $payments)
    {
        $latestPayment = $this->order->payments()->latest()->first();
        $paid = $this->order->status === OrderStatus::Paid || $latestPayment?->status === PaymentStatus::Paid;

        return view('livewire.shop.checkout-payment', [
            'gateways' => $payments->availableGateways(),
            'paid' => $paid,
            'latestPayment' => $latestPayment,
        ]);
    }
}
