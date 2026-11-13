<div class="mx-auto max-w-2xl px-4 py-12 text-center sm:px-6">
    <p class="text-sm text-brand-600">سفارش ثبت شد</p>
    <h1 class="mt-2 text-2xl font-bold text-slate-900">شماره سفارش: {{ $order->number }}</h1>
    <p class="mt-4 text-slate-600">مبلغ قابل پرداخت: <strong class="text-brand-700">{{ number_format($order->total) }} تومان</strong></p>
    <p class="mt-6 text-sm text-slate-500">در مرحله بعد درگاه پرداخت فعال می‌شود.</p>
    <a href="{{ route('home') }}" class="mt-8 inline-block text-brand-700 hover:underline">بازگشت به فروشگاه</a>
</div>
