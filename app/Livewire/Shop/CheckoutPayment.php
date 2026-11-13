<?php

namespace App\Livewire\Shop;

use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.shop')]
class CheckoutPayment extends Component
{
    use AuthorizesRequests;

    public Order $order;

    public function mount(Order $order): void
    {
        $this->authorize('view', $order);
        $this->order = $order->load('items');
    }

    public function render()
    {
        return view('livewire.shop.checkout-payment');
    }
}
