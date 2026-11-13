<div>
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
</div>
