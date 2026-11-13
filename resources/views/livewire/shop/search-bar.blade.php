<form wire:submit="search" class="relative w-full max-w-md">
    <input
        type="search"
        wire:model="q"
        placeholder="جستجوی محصول…"
        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pe-10 ps-4 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-500/20"
    />
    <button type="submit" class="absolute end-2 top-1/2 -translate-y-1/2 rounded-lg bg-brand-600 p-1.5 text-white">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
    </button>
</form>
