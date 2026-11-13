<x-mail::message>
# پرداخت سفارش {{ $order->number }} تأیید شد

مبلغ پرداختی: **{{ number_format($order->total) }} تومان**

<x-mail::button :url="$invoiceUrl">
مشاهده فاکتور
</x-mail::button>

با تشکر،<br>
{{ config('app.name') }}
</x-mail::message>
