@php
    $slides = $banners->isNotEmpty()
        ? $banners
        : collect([
            (object) [
                'title' => '!لباساتو نو کن',
                'subtitle' => 'حراجی پایان سال ' . config('app.name'),
                'link_url' => route('products.index'),
                'image_url' => null,
            ],
        ]);

    $defaultHero = asset('images/hero/bahar-hero.jpg');
@endphp

<section class="bg-slate-100 px-2 pb-6 pt-3 sm:px-4 lg:px-6">
    <div class="hero-carousel hero-clip relative mx-auto max-w-[90rem] bg-slate-900" dir="ltr">
        @if ($slides->count() > 1)
            <input type="radio" name="hero-slide" id="hero-slide-1" class="peer/s1" checked hidden>
            <input type="radio" name="hero-slide" id="hero-slide-2" class="peer/s2" hidden>
        @endif

        <div
            @class([
                'hero-carousel__slides',
                'w-[200%]' => $slides->count() > 1,
                'w-full' => $slides->count() <= 1,
            ])
            @if ($slides->count() > 1)
                style="transform: translateX(0%);"
            @endif
        >
            @foreach ($slides->take(2) as $index => $banner)
                <article class="hero-carousel__slide relative min-h-[220px] sm:min-h-[280px] md:min-h-[340px]">
                    <img
                        src="{{ $banner->image_url ?: $defaultHero }}"
                        alt=""
                        width="1400"
                        height="360"
                        @if ($loop->first)
                            loading="eager"
                            fetchpriority="high"
                        @else
                            loading="lazy"
                        @endif
                        decoding="async"
                        class="absolute inset-0 size-full object-cover object-center"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/25 to-black/10"></div>

                    <div class="relative z-10 flex min-h-[220px] flex-col items-center justify-center px-6 py-10 text-center text-white sm:min-h-[280px] md:min-h-[340px]" dir="rtl">
                        <h1 class="text-3xl font-black sm:text-4xl md:text-5xl">{{ $banner->title }}</h1>
                        @if (! empty($banner->subtitle))
                            <p class="mt-2 text-sm font-medium text-accent-light sm:text-base">{{ $banner->subtitle }}</p>
                        @endif
                        <p class="mt-4 text-xs font-semibold tracking-[0.35em] text-white/80 sm:text-sm">UP TO</p>
                        <p class="hero-offer-bar mt-1">۴۰% OFF</p>
                        <a
                            href="{{ $banner->link_url ?: route('products.index') }}"
                            class="mt-6 inline-flex rounded-full bg-accent px-8 py-2.5 text-sm font-bold text-white shadow-lg shadow-accent/30 transition hover:bg-accent/90"
                        >
                            مشاهده و خرید
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <label for="hero-slide-1" class="hero-nav-btn absolute start-4 top-1/2 z-20 -translate-y-1/2 cursor-pointer" aria-label="اسلاید قبل">
                ‹
            </label>
            <label for="hero-slide-2" class="hero-nav-btn absolute end-4 top-1/2 z-20 -translate-y-1/2 cursor-pointer" aria-label="اسلاید بعد">
                ›
            </label>

            <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                <label for="hero-slide-1" class="cursor-pointer p-1"><span class="hero-dot"></span></label>
                <label for="hero-slide-2" class="cursor-pointer p-1"><span class="hero-dot"></span></label>
            </div>
        @endif
    </div>
</section>