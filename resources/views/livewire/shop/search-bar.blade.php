<form wire:submit="search" class="relative w-full">
    <input
        type="search"
        wire:model="q"
        placeholder="جستجو در {{ config('app.name') }} …"
        class="shop-search-pill"
    />
    <button type="submit" class="absolute end-2 top-1/2 flex size-9 -translate-y-1/2 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-50 hover:text-accent" aria-label="جستجو">
        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/>
        </svg>
    </button>
</form>