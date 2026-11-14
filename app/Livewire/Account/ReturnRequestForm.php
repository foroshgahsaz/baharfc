<?php

namespace App\Livewire\Account;

use App\Enums\OrderStatus;
use App\Enums\ReturnRequestStatus;
use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ReturnRequestForm extends Component
{
    public ?int $orderId = null;

    public string $reason = '';

    public function submit(): void
    {
        $this->validate([
            'orderId' => ['required', 'integer'],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'orderId.required' => 'سفارش را انتخاب کنید.',
            'reason.required' => 'دلیل مرجوعی را بنویسید.',
            'reason.min' => 'دلیل مرجوعی حداقل ۱۰ کاراکتر باشد.',
        ]);

        $order = Order::query()->findOrFail($this->orderId);

        if ($order->user_id !== Auth::id()) {
            throw ValidationException::withMessages(['orderId' => 'سفارش نامعتبر است.']);
        }

        if (! in_array($order->status, [OrderStatus::Paid, OrderStatus::Shipped, OrderStatus::Delivered], true)) {
            throw ValidationException::withMessages(['orderId' => 'این سفارش هنوز قابل مرجوعی نیست.']);
        }

        $exists = ReturnRequest::query()
            ->where('order_id', $order->id)
            ->whereIn('status', [ReturnRequestStatus::Pending, ReturnRequestStatus::Approved])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['orderId' => 'برای این سفارش درخواست باز دارید.']);
        }

        ReturnRequest::query()->create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'reason' => $this->reason,
            'status' => ReturnRequestStatus::Pending,
        ]);

        $this->reset(['orderId', 'reason']);
        session()->flash('return_status', 'درخواست مرجوعی ثبت شد.');
    }

    public function render()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->whereIn('status', [
                OrderStatus::Paid,
                OrderStatus::Shipped,
                OrderStatus::Delivered,
            ])
            ->latest()
            ->get();

        $requests = ReturnRequest::query()
            ->where('user_id', Auth::id())
            ->with('order')
            ->latest()
            ->limit(20)
            ->get();

        return view('livewire.account.return-request-form', [
            'orders' => $orders,
            'requests' => $requests,
        ]);
    }
}
