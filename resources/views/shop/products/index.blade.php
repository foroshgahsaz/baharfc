@extends('layouts.shop')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900">
                {{ $category?->name ?? 'همه محصولات' }}
            </h1>
            @if ($category?->description)
                <p class="mt-2 text-sm text-slate-600">{{ $category->description }}</p>
            @endif
        </div>

        <div class="grid gap-8 lg:grid-cols-4">
            <aside class="space-y-6 lg:col-span-1">
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <h2 class="mb-3 text-sm font-semibold text-slate-900">دسته‌بندی</h2>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="{{ route('products.index') }}" @class(['font-medium text-brand-700' => ! $category, 'text-slate-600 hover:text-brand-600' => (bool) $category])>
                                همه
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                            <li>
                                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" @class(['font-medium text-brand-700' => $category?->id === $cat->id, 'text-slate-600 hover:text-brand-600' => $category?->id !== $cat->id])>
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            <div class="lg:col-span-3">
                <form method="get" class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                    @if ($category)
                        <input type="hidden" name="category" value="{{ $category->slug }}" />
                    @endif
                    @if (! empty($search))
                        <input type="hidden" name="q" value="{{ $search }}" />
                        <p class="text-sm text-brand-700">نتایج جستجو: «{{ $search }}»</p>
                    @endif
                    <div class="flex gap-2 text-sm">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="حداقل قیمت" class="w-28 rounded-lg border-slate-200" />
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="حداکثر قیمت" class="w-28 rounded-lg border-slate-200" />
                        <button type="submit" class="rounded-lg bg-slate-100 px-3 py-1">اعمال</button>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        مرتب‌سازی
                        <select name="sort" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm">
                            <option value="newest" @selected($sort === 'newest')>جدیدترین</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>ارزان‌ترین</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>گران‌ترین</option>
                            <option value="name" @selected($sort === 'name')>نام</option>
                        </select>
                    </label>
                    <p class="text-sm text-slate-500">{{ $products->total() }} محصول</p>
                </form>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($products as $product)
                        <x-shop.product-card :product="$product" />
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">
                            محصولی یافت نشد.
                        </p>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
