@if ($adminBar instanceof \App\Support\AdminBar\AdminBarPresenter)
    <div
        class="baharfc-admin-bar baharfc-admin-bar--{{ $adminBar->context }}"
        role="navigation"
        aria-label="نوار مدیریت"
        dir="rtl"
    >
        <div class="baharfc-admin-bar__inner">
            <a class="baharfc-admin-bar__brand" href="{{ $adminBar->shopHomeUrl }}">
                {{ $adminBar->siteName }}
            </a>

            <span class="baharfc-admin-bar__sep" aria-hidden="true">|</span>

            @foreach ($adminBar->links as $link)
                <a
                    href="{{ $link->url }}"
                    @class([
                        'baharfc-admin-bar__link',
                        'baharfc-admin-bar__link--highlight' => $link->highlight,
                    ])
                    @if ($link->openInNewTab) target="_blank" rel="noopener noreferrer" @endif
                >
                    {{ $link->label }}
                </a>
            @endforeach

            <span class="baharfc-admin-bar__fill" aria-hidden="true"></span>

            <span class="baharfc-admin-bar__user">
                سلام، {{ $adminBar->user->name }}
            </span>

            <a class="baharfc-admin-bar__link" href="{{ $adminBar->panelHomeUrl }}">
                پنل مدیریت
            </a>

            @if ($adminBar->context === 'shop')
                <form action="{{ route('logout') }}" method="post" class="baharfc-admin-bar__logout">
                    @csrf
                    <button type="submit" class="baharfc-admin-bar__link baharfc-admin-bar__link--button">
                        خروج
                    </button>
                </form>
            @else
                <form action="{{ $adminBar->logoutUrl }}" method="post" class="baharfc-admin-bar__logout">
                    @csrf
                    <button type="submit" class="baharfc-admin-bar__link baharfc-admin-bar__link--button">
                        خروج
                    </button>
                </form>
            @endif
        </div>
    </div>
@endif