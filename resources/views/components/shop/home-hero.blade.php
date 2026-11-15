@php
    use App\Models\Banner;

    $defaultHero = asset('images/lebasino/ref-hero.jpg');
    $hasSecond = $banners->count() > 1;

    $slides = $banners->isNotEmpty()
        ? $banners->take(2)
        : collect([(object) [
            'title' => '!لباساتو نو کن',
            'subtitle' => 'حراجی پایان سال لباسینو',
            'link_url' => route('products.index'),
            'image_url' => null,
        ]]);
@endphp

<section class="bg-white pb-5 pt-1">
    <div class="lebasino-shell hero-carousel relative">
        @if ($hasSecond)
            <input type="radio" name="hero-slide" id="hero-slide-1" checked hidden>
            <input type="radio" name="hero-slide" id="hero-slide-2" hidden>
        @endif

        <div @class(['hero-carousel__slides', 'w-[200%]' => $hasSecond])>
            @foreach ($slides as $banner)
                @php
                    $img = $banner->image_url ?: $defaultHero;
                    $baked = blank($banner->image_url);
                    $showOverlay = $banner instanceof Banner;
                    $link = $banner->link_url ?: route('products.index');
                @endphp
                <article class="hero-carousel__slide relative">
                    <div class="lebasino-hero-frame bg-[#1d1d1f]">
                        <img
                            src="{{ $img }}"
                            alt=""
                            width="1180"
                            height="240"
                            @if ($loop->first)
                                loading="eager"
                                fetchpriority="high"
                            @else
                                loading="lazy"
                            @endif
                            decoding="async"
                            class="block h-auto w-full object-cover"
                        />

                        @if ($showOverlay)
                            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center pt-2 text-center text-white" dir="rtl">
                                <h1 class="text-[1.65rem] font-black drop-shadow sm:text-[2rem]">{{ $banner->title }}</h1>
                                @if (! empty($banner->subtitle))
                                    <p class="mt-1 text-[13px] font-semibold text-accent-light">{{ $banner->subtitle }}</p>
                                @endif
                                <div class="lebasino-hero-offer mt-3">
                                    <span class="lebasino-hero-upto">UP TO</span>
                                    <span class="lebasino-hero-percent">۴۰%<span class="text-[0.55em] font-bold">OFF</span></span>
                                </div>
                            </div>
                        @endif

                        <a href="{{ $link }}" class="lebasino-hero-btn absolute bottom-[13%] left-1/2 z-10 -translate-x-1/2">
                            مشاهده و خرید
                        </a>

                        @if ($hasSecond)
                            <label for="hero-slide-1" class="lebasino-hero-arrow start-3 cursor-pointer sm:start-5" aria-label="اسلاید قبل">‹</label>
                            <label for="hero-slide-2" class="lebasino-hero-arrow end-3 cursor-pointer sm:end-5" aria-label="اسلاید بعد">›</label>
                        @else
                            <span class="lebasino-hero-arrow start-3 sm:start-5" aria-hidden="true">‹</span>
                            <span class="lebasino-hero-arrow end-3 sm:end-5" aria-hidden="true">›</span>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-2 flex justify-center gap-1.5">
            <span @class(['hero-dot', 'bg-accent' => ! $hasSecond])></span>
            <span class="hero-dot"></span>
        </div>
    </div>
</section>