<form wire:submit="search" class="search" wire:click.outside="closeResults">
    <input
        type="search"
        wire:model.live.debounce.250ms="q"
        placeholder="جستجوی محصولات ..."
        aria-label="جستجوی محصولات"
        autocomplete="off"
    >
    <button type="submit" aria-label="جستجو">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg>
    </button>

    @if ($open)
        <div class="search-results" role="listbox">
            @forelse ($results as $item)
                <a href="{{ route('products.show', $item['slug']) }}" wire:navigate role="option">
                    <span>{{ $item['name'] }}</span>
                    <span class="text-xs text-[var(--lebasino-muted)]"> — {{ $item['formatted_price'] }}</span>
                </a>
            @empty
                <p class="search-results-empty">محصولی یافت نشد</p>
            @endforelse
        </div>
    @endif
</form>