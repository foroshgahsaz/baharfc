<div class="shop-page container">
    <header class="shop-page-head">
        <h1>تسویه حساب</h1>
        <p class="shop-page-sub">آدرس تحویل را انتخاب کنید و سفارش را نهایی کنید.</p>
    </header>

    @error('checkout') <p class="shop-alert shop-alert-error">{{ $message }}</p> @enderror

    <livewire:shop.checkout-address-modal />

    <form wire:submit="placeOrder" class="shop-checkout-layout">
        <div class="shop-checkout-main">
            <section class="shop-panel">
                <div class="shop-panel-head">
                    <h2>آدرس تحویل</h2>
                    <button type="button" class="shop-btn shop-btn-outline shop-btn-sm" wire:click="openAddressModal">
                        + آدرس جدید
                    </button>
                </div>

                @if ($addresses->isEmpty())
                    <p class="shop-panel-empty">هنوز آدرسی ثبت نکرده‌اید. با دکمه «آدرس جدید» همین‌جا ثبت کنید.</p>
                @else
                    <ul class="shop-address-list">
                        @foreach ($addresses as $address)
                            <li>
                                <label class="shop-address-card {{ $addressId === $address->id ? 'is-selected' : '' }}">
                                    <input type="radio" wire:model="addressId" value="{{ $address->id }}" />
                                    <span class="shop-address-card-body">
                                        <span class="shop-address-title">{{ $address->title }}</span>
                                        <span class="shop-address-meta">{{ $address->recipient_name }} — {{ $address->phone }}</span>
                                        <span class="shop-address-line">{{ $address->fullLocation() }} — {{ $address->address_line }}</span>
                                        @if ($address->latitude && $address->longitude)
                                            <span class="shop-address-pin">موقعیت روی نقشه ثبت شده</span>
                                        @endif
                                    </span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    @error('addressId') <p class="shop-field-error">{{ $message }}</p> @enderror
                @endif
            </section>

            <section class="shop-panel">
                <label class="shop-field shop-field-full">
                    <span>یادداشت سفارش (اختیاری)</span>
                    <textarea wire:model="customerNote" rows="3" class="shop-input"></textarea>
                </label>
            </section>
        </div>

        <aside class="shop-summary">
            <h2>خلاصه سفارش</h2>
            <ul class="shop-checkout-lines">
                @foreach ($items as $item)
                    <li>
                        <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span>{{ number_format($item->lineTotal()) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="shop-summary-lines">
                <div><dt>جمع</dt><dd>{{ number_format($subtotal) }} تومان</dd></div>
                @if ($discount)
                    <div class="shop-summary-discount"><dt>تخفیف</dt><dd>−{{ number_format($discount) }}</dd></div>
                @endif
                <div>
                    <dt>ارسال @if($shippingMethod) ({{ $shippingMethod->name }}) @endif</dt>
                    <dd>{{ number_format($shipping) }} تومان</dd>
                </div>
                <div class="shop-summary-total"><dt>مبلغ نهایی</dt><dd>{{ number_format($total) }} تومان</dd></div>
            </dl>
            <button
                type="submit"
                @disabled($addresses->isEmpty())
                class="shop-btn shop-btn-primary shop-btn-block"
            >
                ثبت سفارش و ادامه پرداخت
            </button>
        </aside>
    </form>
</div>