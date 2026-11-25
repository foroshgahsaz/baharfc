<div @class(['fs-product-cart-root' => $ui === 'product-page'])>
    @if ($ui !== 'product-page' && $product->isVariable() && $product->activeVariants->isNotEmpty())
        <div class="mb-4">
            <p class="mb-2 text-sm font-medium text-slate-700">انتخاب گزینه</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($product->activeVariants as $variant)
                    <button
                        type="button"
                        wire:click="$set('productVariantId', {{ $variant->id }})"
                        @class([
                            'rounded-xl border px-3 py-2 text-sm transition',
                            $productVariantId === $variant->id
                                ? 'border-brand-600 bg-brand-50 text-brand-800'
                                : 'border-slate-200 text-slate-700 hover:border-brand-300',
                        ])
                    >
                        <span>{{ $variant->name }}</span>
                        <span class="mr-2 text-xs text-slate-500">{{ number_format($variant->price) }} تومان</span>
                    </button>
                @endforeach
            </div>
            @error('variant')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif

    @if ($ui === 'product-page')
        <div class="fs-product-cart-row">
            <div class="fs-product-qty" role="group" aria-label="تعداد">
                <button type="button" class="fs-product-qty-btn" wire:click="incrementQuantity" aria-label="افزایش">+</button>
                <input
                    type="number"
                    min="1"
                    max="99"
                    wire:model.blur="quantity"
                    class="fs-product-qty-input"
                    aria-label="تعداد"
                />
                <button type="button" class="fs-product-qty-btn" wire:click="decrementQuantity" aria-label="کاهش">−</button>
            </div>
            <button
                type="button"
                wire:click="add"
                wire:loading.attr="disabled"
                class="fs-product-add-btn"
                data-add-to-cart-trigger
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span wire:loading.remove wire:target="add">افزودن به سبد خرید</span>
                <span wire:loading wire:target="add">در حال افزودن…</span>
            </button>
        </div>
        @error('variant')
            <p class="fs-product-cart-error">{{ $message }}</p>
        @enderror
        @if (session('cart_toast'))
            <p class="fs-product-cart-toast">{{ session('cart_toast') }}</p>
        @endif
    @else
        <div class="flex flex-wrap items-center gap-3">
            <label class="text-sm text-slate-600">
                تعداد
                <input
                    type="number"
                    min="1"
                    max="99"
                    wire:model="quantity"
                    class="ms-2 w-16 rounded-lg border border-slate-200 px-2 py-1 text-sm"
                />
            </label>
            <button
                type="button"
                wire:click="add"
                wire:loading.attr="disabled"
                class="rounded-2xl bg-brand-600 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 disabled:opacity-60 sm:w-auto"
            >
                <span wire:loading.remove wire:target="add">افزودن به سبد خرید</span>
                <span wire:loading wire:target="add">در حال افزودن…</span>
            </button>
        </div>
        @if (session('cart_toast'))
            <p class="mt-2 text-sm text-brand-700">{{ session('cart_toast') }}</p>
        @endif
    @endif
</div>

@if ($ui === 'product-page')
    @script
    <script>
        $wire.on('product-variant-selected', ({ variantId }) => {
            if (variantId != null) {
                $wire.set('productVariantId', variantId);
            }
        });

        $wire.el.querySelector('[data-add-to-cart-trigger]')?.addEventListener('click', () => {
            const page = document.querySelector('.fs-product-page');
            const id = page?.dataset?.selectedVariantId;
            if (id) {
                $wire.set('productVariantId', Number(id));
            }
        }, { capture: true });
    </script>
    @endscript
@endif