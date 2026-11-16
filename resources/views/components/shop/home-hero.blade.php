@props(['banners'])

@php
    $slides = $banners->filter(fn (\App\Models\Banner $banner) => filled($banner->slideImageUrl()));
@endphp

<section class="hero" id="homeHero" aria-label="پیشنهادهای ویژه">
    @if ($slides->isEmpty())
        <a class="hero-link" href="{{ route('products.index') }}" data-slide-index="0" id="heroImage">
            <img
                src="{{ asset('images/lebasino/ref-hero.jpg') }}"
                alt="پیشنهاد ویژه فروشگاه"
                width="1388"
                height="463"
                loading="eager"
                fetchpriority="high"
                decoding="async"
            >
        </a>
    @else
        <button class="slider-arrow prev" type="button" aria-label="اسلاید قبلی">‹</button>

        @foreach ($slides as $index => $banner)
            <a
                class="hero-link"
                href="{{ $banner->link_url ?: route('products.index') }}"
                data-slide-index="{{ $index }}"
                @if ($index > 0) hidden @endif
                @if ($index === 0) id="heroImage" @endif
            >
                <img
                    src="{{ $banner->slideImageUrl() }}"
                    alt="{{ $banner->title }}"
                    width="1388"
                    height="463"
                    @if ($index === 0)
                        loading="eager"
                        fetchpriority="high"
                    @else
                        loading="lazy"
                    @endif
                    decoding="async"
                >
            </a>
        @endforeach

        <button class="slider-arrow next" type="button" aria-label="اسلاید بعدی">›</button>

        @if ($slides->count() > 1)
            <div class="slider-dots">
                @foreach ($slides as $index => $banner)
                    <button
                        type="button"
                        class="dot {{ $index === 0 ? 'active' : '' }}"
                        aria-label="اسلاید {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    @endif
</section>