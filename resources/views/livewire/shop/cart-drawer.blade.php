<div>
    <div
        class="cart-drawer-backdrop {{ $open ? 'is-open' : '' }}"
        wire:click="close"
        aria-hidden="true"
    ></div>

    <aside
        class="cart-drawer {{ $open ? 'is-open' : '' }}"
        role="dialog"
        aria-modal="true"
        aria-label="سبد خرید"
        @if (! $open) hidden @endif
    >
        <header class="cart-drawer-head">
            <h2>سبد خرید شما</h2>
            <button type="button" class="cart-drawer-close" wire:click="close" aria-label="بستن">×</button>
        </header>

        @if ($highlight)
            <div class="cart-drawer-added">
                <p class="cart-drawer-added-label">به سبد اضافه شد</p>
                <div class="cart-drawer-added-row">
                    <div class="cart-drawer-thumb">
                        @if ($highlight->imageUrl())
                            <img src="{{ $highlight->imageUrl() }}" alt="">
                        @endif
                    </div>
                    <div>
                        <p class="cart-drawer-item-title">{{ $highlight->name }}</p>
                        <p class="cart-drawer-item-price">{{ $highlight->formattedPrice() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <ul class="cart-drawer-list">
            @forelse ($items as $item)
                <li class="cart-drawer-item">
                    <div class="cart-drawer-thumb">
                        @if ($item->product->imageUrl())
                            <img src="{{ $item->product->imageUrl() }}" alt="">
                        @endif
                    </div>
                    <div class="cart-drawer-item-body">
                        <p class="cart-drawer-item-title">{{ $item->product->name }}</p>
                        <p class="cart-drawer-item-meta">تعداد: {{ $item->quantity }}</p>
                        <p class="cart-drawer-item-price">{{ number_format($item->lineTotal()) }} تومان</p>
                    </div>
                </li>
            @empty
                <li class="cart-drawer-empty">سبد خرید خالی است.</li>
            @endforelse
        </ul>

        <footer class="cart-drawer-foot">
            <div class="cart-drawer-total">
                <span>جمع</span>
                <strong>{{ number_format($total) }} تومان</strong>
            </div>
            <div class="cart-drawer-actions">
                <a href="{{ route('cart.index') }}" class="shop-btn shop-btn-outline" wire:click="close" wire:navigate>مشاهده سبد</a>
                @auth
                    <a href="{{ route('checkout.index') }}" class="shop-btn shop-btn-primary" wire:click="close" wire:navigate>تکمیل خرید</a>
                @else
                    <a href="{{ route('login') }}" class="shop-btn shop-btn-primary" wire:navigate>ورود و تکمیل خرید</a>
                @endauth
            </div>
        </footer>
    </aside>
</div>