@props(['product'])

<article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
        @if ($product->imageUrl())
            <img
                src="{{ $product->imageUrl() }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
            />
        @else
            <div class="flex h-full items-center justify-center text-4xl text-slate-300">📦</div>
        @endif
        @if ($product->compare_at_price && $product->compare_at_price > $product->price)
            <span class="absolute start-3 top-3 rounded-lg bg-red-500 px-2 py-0.5 text-xs font-semibold text-white">
                تخفیف
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-4">
        <h3 class="line-clamp-2 text-sm font-semibold text-slate-900">{{ $product->name }}</h3>
        <div class="mt-3 flex items-center justify-between gap-2">
            <p class="text-base font-bold text-brand-700">{{ $product->formattedPrice() }}</p>
            @if ($product->compare_at_price)
                <p class="text-xs text-slate-400 line-through">{{ number_format($product->compare_at_price) }}</p>
            @endif
        </div>
        <button type="button" class="mt-4 w-full rounded-xl border border-slate-200 py-2 text-sm font-medium text-slate-700 transition hover:border-brand-400 hover:text-brand-700">
            افزودن به سبد
        </button>
    </div>
</article>
