<a href="{{ route('cart.index') }}" wire:navigate class="cart-header-link">
    <span class="bag" aria-hidden="true">♧</span>
    <span class="link-text">{{ number_format($cartSubtotal) }} تومان</span>
    <b>{{ $cartItemCount }}</b>
</a>