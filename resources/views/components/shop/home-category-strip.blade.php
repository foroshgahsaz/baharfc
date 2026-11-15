<section class="border-b border-lebasino-border bg-white pb-3 pt-1">
    <div class="lebasino-shell">
        <div class="flex items-start gap-3 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="lebasino-new-box">
                <span class="px-1 text-center leading-tight">بار جدید رسید!</span>
                <span class="text-accent" aria-hidden="true">›››</span>
            </a>

            @foreach (config('shop.demo_category_icons') as $item)
                <a href="{{ route('products.index', ['category' => $item['slug']]) }}" class="group flex shrink-0 flex-col items-center">
                    <div class="lebasino-cat-orbit">
                        <img
                            src="{{ asset($item['image']) }}"
                            alt=""
                            width="64"
                            height="64"
                            loading="lazy"
                            decoding="async"
                            class="size-full rounded-full object-cover"
                        />
                    </div>
                    <span class="lebasino-cat-label group-hover:text-accent">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>