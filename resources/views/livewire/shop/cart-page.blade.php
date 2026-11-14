<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-slate-900">سبد خرید</h1>

    @if (session('cart_status'))
        <p class="mt-4 rounded-2xl bg-brand-50 px-4 py-3 text-sm text-brand-800">{{ session('cart_status') }}</p>
    @endif
    @error('cart') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

    @if ($items->isEmpty())
        <p class="mt-10 text-center text-slate-500">سبد خرید شما خالی است.</p>
        <div class="mt-6 text-center">
            <a href="{{ route('products.index') }}" class="text-brand-700 hover:underline">مشاهده محصولات</a>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <ul class="space-y-4 lg:col-span-2">
                @foreach ($items as $item)
                    <li class="flex gap-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
                            @if ($item->product->imageUrl())
                                <img src="{{ $item->product->imageUrl() }}" alt="" class="h-full w-full object-cover" />
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-semibold text-slate-900 hover:text-brand-700">
                                {{ $item->product->name }}
                            </a>
                            <p class="mt-1 text-sm text-brand-700">{{ $item->product->formattedPrice() }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <input
                                    type="number"
                                    min="1"
                                    max="99"
                                    value="{{ $item->quantity }}"
                                    wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                    class="w-16 rounded-lg border border-slate-200 px-2 py-1 text-sm"
                                />
                                <button type="button" wire:click="removeItem({{ $item->id }})" class="text-xs text-red-600 hover:underline">حذف</button>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-800">{{ number_format($item->lineTotal()) }} تومان</p>
                    </li>
                @endforeach
            </ul>

            <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">جمع‌بندی</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-600">جمع جزء</dt>
                        <dd>{{ number_format($subtotal) }} تومان</dd>
                    </div>
                    @if ($discount > 0)
                        <div class="flex justify-between text-green-700">
                            <dt>تخفیف @if($appliedCoupon) ({{ $appliedCoupon->code }}) @endif</dt>
                            <dd>−{{ number_format($discount) }} تومان</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-slate-600">ارسال</dt>
                        <dd>{{ number_format($shipping) }} تومان</dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-3 text-base font-bold">
                        <dt>قابل پرداخت</dt>
                        <dd class="text-brand-700">{{ number_format($total) }} تومان</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <label class="text-xs font-medium text-slate-600">روش ارسال</label>
                    <select wire:model.live="shippingMethodId" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                        @foreach ($shippingMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }} — {{ $method->formattedPrice() }}</option>
                        @endforeach
                    </select>
                </div>

                <form wire:submit="applyCoupon" class="mt-6 flex gap-2">
                    <input type="text" wire:model="couponCode" placeholder="کد تخفیف" class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm" dir="ltr" />
                    <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm text-white hover:bg-slate-900">اعمال</button>
                </form>
                @error('couponCode') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @if ($appliedCoupon)
                    <button type="button" wire:click="removeCoupon" class="mt-2 text-xs text-slate-500 hover:underline">حذف کد تخفیف</button>
                @endif

                @auth
                    <a href="{{ route('checkout.index') }}" class="mt-6 block w-full rounded-2xl bg-brand-600 py-3 text-center text-sm font-semibold text-white hover:bg-brand-700">
                        ادامه تسویه حساب
                    </a>
                @else
                    <a href="{{ route('login') }}" class="mt-6 block w-full rounded-2xl border border-brand-300 py-3 text-center text-sm font-semibold text-brand-700 hover:bg-brand-50">
                        برای تسویه حساب وارد شوید
                    </a>
                @endauth
            </aside>
        </div>
    @endif
</div>
