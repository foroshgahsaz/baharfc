<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class OrderInvoiceController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Order $order): View|Response|HttpResponse
    {
        $this->authorize('view', $order);

        $order->load(['items', 'user']);

        if ($request->boolean('download')) {
            return Pdf::loadView('shop.orders.invoice', ['order' => $order])
                ->setPaper('a4')
                ->download("invoice-{$order->number}.pdf");
        }

        if ($request->get('format') === 'pdf') {
            return Pdf::loadView('shop.orders.invoice', ['order' => $order])
                ->setPaper('a4')
                ->stream("invoice-{$order->number}.pdf");
        }

        return view('shop.orders.invoice', [
            'order' => $order,
            'printable' => true,
        ]);
    }
}
