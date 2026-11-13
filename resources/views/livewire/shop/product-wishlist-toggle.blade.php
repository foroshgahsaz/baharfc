<div>
    <button
        type="button"
        wire:click="toggle"
        class="inline-flex items-center gap-2 rounded-2xl border px-4 py-2.5 text-sm font-medium transition {{ $wishlisted ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 text-slate-700 hover:bg-slate-50' }}"
    >
        <span>{{ $wishlisted ? '♥' : '♡' }}</span>
        {{ $wishlisted ? 'در علاقه‌مندی‌ها' : 'افزودن به علاقه‌مندی‌ها' }}
    </button>
</div>
