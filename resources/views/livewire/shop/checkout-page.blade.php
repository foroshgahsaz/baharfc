<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-slate-900">تسویه حساب</h1>

    @error('checkout') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

    <form wire:submit="placeOrder" class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-900">آدرس تحویل</h2>
                @if ($addresses->isEmpty())
                    <p class="mt-4 text-sm text-slate-600">
                        ابتدا در
                        <a href="{{ route('account.index') }}" class="text-brand-700 hover:underline">حساب کاربری</a>
                        یک آدرس ثبت کنید.
                    </p>
                @else
                    <ul class="mt-4 space-y-3">
                        @foreach ($addresses as $address)
                            <li>
                                <label class="flex cursor-pointer gap-3 rounded-2xl border p-4 transition {{ $addressId === $address->id ? 'border-brand-500 bg-brand-50' : 'border-slate-200 hover:border-slate-300' }}">
                                    <input type="radio" wire:model="addressId" value="{{ $address->id }}" class="mt-1 text-brand-600" />
                                    <span>
                                        <span class="font-medium">{{ $address->title }}</span>
                                        <span class="block text-sm text-slate-600">{{ $address->recipient_name }} — {{ $address->phone }}</span>
                                        <span class="block text-sm text-slate-700">{{ $address->fullLocation() }} — {{ $address->address_line }}</span>
                                    </span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    @error('addressId') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                @endif
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <label class="block text-sm font-medium text-slate-700">یادداشت سفارش (اختیاری)</label>
                <textarea wire:model="customerNote" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"></textarea>
            </section>
        </div>

        <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-slate-900">خلاصه سفارش</h2>
            <ul class="mt-4 space-y-2 text-sm text-slate-700">
                @foreach ($items as $item)
                    <li class="flex justify-between gap-2">
                        <span class="truncate">{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span>{{ number_format($item->lineTotal()) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-sm">
                <div class="flex justify-between"><dt>جمع</dt><dd>{{ number_format($subtotal) }} تومان</dd></div>
                @if ($discount)
                    <div class="flex justify-between text-green-700"><dt>تخفیف</dt><dd>−{{ number_format($discount) }}</dd></div>
                @endif
                <div class="flex justify-between">
                    <dt>ارسال @if($shippingMethod) ({{ $shippingMethod->name }}) @endif</dt>
                    <dd>{{ number_format($shipping) }} تومان</dd>
                </div>
                <div class="flex justify-between text-base font-bold text-brand-700"><dt>مبلغ نهایی</dt><dd>{{ number_format($total) }} تومان</dd></div>
            </dl>
            <button
                type="submit"
                @disabled($addresses->isEmpty())
                class="mt-6 w-full rounded-2xl bg-brand-600 py-3 text-sm font-semibold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                ثبت سفارش و ادامه پرداخت
            </button>
        </aside>
    </form>
</div>
