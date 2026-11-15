<section class="border-b border-slate-100 bg-white">
    <div class="mx-auto flex max-w-[90rem] items-stretch gap-3 overflow-x-auto px-4 py-4 sm:px-6 lg:px-8 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <a
            href="{{ route('products.index', ['sort' => 'newest']) }}"
            class="flex shrink-0 flex-col items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs font-semibold leading-relaxed text-slate-700 transition hover:border-accent/40 hover:text-accent"
        >
            <span class="mb-2 tracking-wide">بار جدید رسید!</span>
            <span class="text-lg text-accent" aria-hidden="true">›››</span>
        </a>

        @forelse ($iconCategories as $category)
            <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group flex shrink-0 flex-col items-center gap-2 px-1">
                <div class="category-orbit">
                    @if ($category->imageUrl())
                        <img
                            src="{{ $category->imageUrl() }}"
                            alt=""
                            width="80"
                            height="80"
                            loading="lazy"
                            decoding="async"
                            class="size-full rounded-full object-cover"
                        />
                    @else
                        <span class="text-lg font-bold text-accent/70">{{ mb_substr($category->name, 0, 1) }}</span>
                    @endif
                </div>
                <span class="max-w-[5.5rem] text-center text-xs text-slate-700 transition group-hover:text-accent">{{ $category->name }}</span>
            </a>
        @empty
            @foreach (['بلوز زنانه', 'ژاکت مردانه', 'شلوار زنانه', 'تی‌شرت مردانه', 'دامن زنانه', 'اکسسوری'] as $label)
                <div class="flex shrink-0 flex-col items-center gap-2 px-1 opacity-60">
                    <div class="category-orbit">
                        <span class="text-sm text-accent">+</span>
                    </div>
                    <span class="max-w-[5.5rem] text-center text-xs text-slate-600">{{ $label }}</span>
                </div>
            @endforeach
        @endforelse
    </div>
</section>