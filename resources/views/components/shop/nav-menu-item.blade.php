@props(['item', 'depth' => 0])

@php
    $children = $item->relationLoaded('children')
        ? $item->children->where('is_active', true)->sortBy('sort_order')
        : collect();
    $hasChildren = $children->isNotEmpty();
    $isMega = $depth === 0 && $item->hasMegaPanel();
@endphp

<li @class([
    'nav-item',
    'nav-item--has-children' => $hasChildren && ! $isMega,
    'nav-item--mega' => $isMega,
])>
    <a
        href="{{ $item->resolvedUrl() }}"
        @class([
            'nav-link',
            'nav-divider' => $item->show_divider_before,
            'is-active' => $item->isCurrent(),
        ])
        @if ($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
        @if ($hasChildren && ! $isMega) aria-haspopup="true" @endif
    >
        {{ $item->label }}
        @if ($hasChildren && ! $isMega)
            <span class="nav-chevron" aria-hidden="true">▾</span>
        @endif
    </a>

    @if ($isMega)
        <div class="mega-menu-panel">
            <div class="mega-menu-inner container">
                @foreach ($children as $column)
                    @php
                        $subLinks = $column->relationLoaded('children')
                            ? $column->children->where('is_active', true)->sortBy('sort_order')
                            : collect();
                    @endphp
                    <div class="mega-menu-column">
                        <a href="{{ $column->resolvedUrl() }}" class="mega-menu-column-title">{{ $column->label }}</a>
                        @foreach ($subLinks as $sub)
                            <a
                                href="{{ $sub->resolvedUrl() }}"
                                class="mega-menu-sublink"
                                @if ($sub->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                            >{{ $sub->label }}</a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @elseif ($hasChildren)
        <ul class="nav-submenu">
            @foreach ($children as $child)
                <x-shop.nav-menu-item :item="$child" :depth="$depth + 1" />
            @endforeach
        </ul>
    @endif
</li>