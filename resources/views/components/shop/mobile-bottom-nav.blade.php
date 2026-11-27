@props(['items' => []])

@if (! empty($items))
    <nav class="mobile-bottom-nav" aria-label="منوی پایین موبایل">
        @foreach ($items as $item)
            <a
                href="{{ $item['href'] }}"
                @class([
                    'mobile-bottom-nav-item',
                    'is-action' => ($item['key'] ?? '') === 'search',
                ])
                data-mobile-bottom-key="{{ $item['key'] ?? '' }}"
                aria-label="{{ $item['label'] ?? '' }}"
            >
                @if (($item['icon'] ?? '') === 'home')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z" fill="currentColor"/></svg>
                @elseif (($item['icon'] ?? '') === 'grid')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h7v7H4zm9 0h7v7h-7zM4 13h7v7H4zm9 0h7v7h-7z" fill="currentColor"/></svg>
                @elseif (($item['icon'] ?? '') === 'search')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 3a7.5 7.5 0 1 1 0 15 7.5 7.5 0 0 1 0-15m0-2a9.5 9.5 0 1 0 5.74 17.08l4.48 4.49 1.42-1.41-4.49-4.49A9.49 9.49 0 0 0 10.5 1" fill="currentColor"/></svg>
                @elseif (($item['icon'] ?? '') === 'cart')
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4h-2l-1 2h2zm2 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4m8 0a2 2 0 1 0 .01 4 2 2 0 0 0-.01-4M7 6v12a1 1 0 0 0 1 1h10.5a1 1 0 0 0 .97-.76l2-8A1 1 0 0 0 21.5 9H8.5z" fill="currentColor"/></svg>
                @else
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 12c-4.97 0-9 2.24-9 5v1h18v-1c0-2.76-4.03-5-9-5" fill="currentColor"/></svg>
                @endif
                <span>{{ $item['label'] ?? '' }}</span>
            </a>
        @endforeach
    </nav>
@endif