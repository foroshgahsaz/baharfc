<div class="mx-auto max-w-2xl px-4 py-12 sm:px-6">
    @if (session('payment_status') === 'success' || $paid)
        <div class="rounded-3xl border border-green-200 bg-green-50 p-8 text-center">
            <h1 class="text-2xl font-bold text-green-900">پرداخت موفق</h1>
            <p class="mt-2 text-sm text-green-800">سفارش {{ $order->number }} با موفقیت پرداخت شد.</p>
            @if ($latestPayment?->tracking_code)
                <p class="mt-4 text-xs text-green-700" dir="ltr">کد پیگیری: {{ $latestPayment->tracking_code }}</p>
            @endif
            <a href="{{ route('home') }}" class="mt-6 inline-block text-brand-700 hover:underline">بازگشت به فروشگاه</a>
        </div>
    @elseif (session('payment_status') === 'error')
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            پرداخت ناموفق بود. {{ session('payment_message') }}
        </div>
    @endif

    @unless ($paid)
        <h1 class="text-2xl font-bold text-slate-900">پرداخت سفارش {{ $order->number }}</h1>
        <p class="mt-2 text-slate-600">مبلغ: <strong class="text-brand-700">{{ number_format($order->total) }} تومان</strong></p>

        @if ($order->status !== \App\Enums\OrderStatus::PendingPayment)
            <p class="mt-6 text-sm text-slate-500">وضعیت سفارش: {{ $order->statusLabel() }}</p>
        @else
            <form wire:submit="pay" class="mt-8 space-y-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-slate-700">انتخاب درگاه پرداخت</p>
                <ul class="space-y-2">
                    @php
                        $gatewayBadges = [
                            'mellat' => ['بانک', 'bg-blue-100 text-blue-800'],
                            'parsian' => ['بانک', 'bg-indigo-100 text-indigo-800'],
                            'digipay' => ['کیف پول', 'bg-violet-100 text-violet-800'],
                            'snapppay' => ['اقساطی', 'bg-orange-100 text-orange-800'],
                        ];
                    @endphp
                    @foreach ($gateways as $slug => $gatewayDriver)
                        @php [$badgeLabel, $badgeClass] = $gatewayBadges[$slug] ?? ['درگاه', 'bg-slate-100 text-slate-700']; @endphp
                        <li>
                            <label @class([
                                'flex cursor-pointer items-center gap-3 rounded-2xl border p-4 transition',
                                'border-brand-500 bg-brand-50 ring-2 ring-brand-500/20' => $gateway === $slug,
                                'border-slate-200 hover:border-brand-300' => $gateway !== $slug,
                            ])>
                                <input type="radio" wire:model.live="gateway" value="{{ $slug }}" class="text-brand-600" />
                                <span class="flex flex-1 flex-wrap items-center gap-2 text-sm font-medium">
                                    {{ $gatewayDriver->name() }}
                                    <span class="rounded-lg px-2 py-0.5 text-xs {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    @if (config('payment.mode') === 'sandbox' || ! $gatewayDriver->isConfigured())
                                        <span class="text-xs text-amber-600">(حالت آزمایشی)</span>
                                    @endif
                                </span>
                            </label>
                        </li>
                    @endforeach
                    @if (in_array($gateway, ['digipay', 'snapppay'], true))
                        <p class="text-xs leading-relaxed text-slate-500">
                            @if ($gateway === 'digipay')
                                پرداخت از کیف پول و اعتبار دیجی‌پی — در حالت آزمایشی بدون اتصال واقعی انجام می‌شود.
                            @else
                                پرداخت اقساطی اسنپ‌پی — در حالت آزمایشی بدون اتصال واقعی انجام می‌شود.
                            @endif
                        </p>
                    @endif
                </ul>
                <button type="submit" class="w-full rounded-2xl bg-brand-600 py-3 text-sm font-semibold text-white hover:bg-brand-700">
                    پرداخت و اتصال به درگاه
                </button>
            </form>
        @endif
    @endunless
</div>
