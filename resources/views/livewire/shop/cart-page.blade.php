<div class="shop-page container">
    <header class="shop-page-head">
        <h1>سبد خرید</h1>
        <p class="shop-page-sub">محصولات انتخاب‌شده را مرور کنید و برای پرداخت ادامه دهید.</p>
    </header>

    @if (session('cart_status'))
        <p class="shop-alert shop-alert-success">{{ session('cart_status') }}</p>
    @endif
    @error('cart') <p class="shop-alert shop-alert-error">{{ $message }}</p> @enderror

    @if ($items->isEmpty())
        <div class="shop-empty">
            <p>سبد خرید شما خالی است.</p>
            <a href="{{ route('products.index') }}" class="shop-btn shop-btn-primary" wire:navigate>مشاهده محصولات</a>
        </div>
    @else
        <div class="shop-cart-layout">
            <ul class="shop-cart-list">
                @foreach ($items as $item)
                    <li class="shop-cart-item">
                        <a href="{{ route('products.show', $item->product->slug) }}" class="shop-cart-thumb" wire:navigate>
                            @if ($item->product->imageUrl())
                                <img src="{{ $item->product->imageUrl() }}" alt="">
                            @endif
                        </a>
                        <div class="shop-cart-item-body">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="shop-cart-title" wire:navigate>
                                {{ $item->product->name }}
                            </a>
                            <p class="shop-cart-unit">{{ $item->product->formattedPrice() }}</p>
                            <div class="shop-cart-qty">
                                <input
                                    type="number"
                                    min="1"
                                    max="99"
                                    value="{{ $item->quantity }}"
                                    wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                    class="shop-input shop-input-qty"
                                />
                                <button type="button" wire:click="removeItem({{ $item->id }})" class="shop-link-danger">حذف</button>
                            </div>
                        </div>
                        <p class="shop-cart-line-total">{{ number_format($item->lineTotal()) }} <small>تومان</small></p>
                    </li>
                @endforeach
            </ul>

            <aside class="shop-summary">
                <h2>جمع‌بندی</h2>
                <dl class="shop-summary-lines">
                    <div><dt>جمع جزء</dt><dd>{{ number_format($subtotal) }} تومان</dd></div>
                    @if ($discount > 0)
                        <div class="shop-summary-discount">
                            <dt>تخفیف @if($appliedCoupon) ({{ $appliedCoupon->code }}) @endif</dt>
                            <dd>−{{ number_format($discount) }} تومان</dd>
                        </div>
                    @endif
                    <div><dt>ارسال</dt><dd>{{ number_format($shipping) }} تومان</dd></div>
                    <div class="shop-summary-total"><dt>قابل پرداخت</dt><dd>{{ number_format($total) }} تومان</dd></div>
                </dl>

                <label class="shop-field">
                    <span>روش ارسال</span>
                    <select wire:model.live="shippingMethodId" class="shop-input">
                        @foreach ($shippingMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }} — {{ $method->formattedPrice() }}</option>
                        @endforeach
                    </select>
                </label>

                <form wire:submit="applyCoupon" class="shop-coupon-form">
                    <input type="text" wire:model="couponCode" placeholder="کد تخفیف" class="shop-input" dir="ltr" />
                    <button type="submit" class="shop-btn shop-btn-outline">اعمال</button>
                </form>
                @error('couponCode') <p class="shop-field-error">{{ $message }}</p> @enderror
                @if ($appliedCoupon)
                    <button type="button" wire:click="removeCoupon" class="shop-link-muted">حذف کد تخفیف</button>
                @endif

                @auth
                    <a href="{{ route('checkout.index') }}" class="shop-btn shop-btn-primary shop-btn-block" wire:navigate>ادامه تسویه حساب</a>
                @else
                    <a href="{{ route('login') }}" class="shop-btn shop-btn-primary shop-btn-block" wire:navigate>ورود برای تسویه حساب</a>
                @endauth
            </aside>
        </div>
    @endif
</div>