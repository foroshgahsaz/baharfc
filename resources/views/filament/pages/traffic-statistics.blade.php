<x-filament-panels::page>
    <form wire:submit="applyRange" class="mb-6 flex flex-wrap items-end gap-4 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">از تاریخ</label>
            <input type="date" wire:model="fromDate" class="fi-input block rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800" />
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-200">تا تاریخ</label>
            <input type="date" wire:model="toDate" class="fi-input block rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800" />
        </div>
        <x-filament::button type="submit" size="sm">اعمال بازه</x-filament::button>
    </form>

    @php
        $data = $this->getViewData();
    @endphp

    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        بازه: {{ $data['fromLabel'] }} تا {{ $data['toLabel'] }}
    </p>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs text-gray-500">کل بازدید ثبت‌شده</p>
            <p class="mt-1 text-2xl font-bold">{{ number_format($data['totalViews']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs text-gray-500">محصولات</p>
            <p class="mt-1 text-2xl font-bold">{{ number_format($data['productViews']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs text-gray-500">بلاگ</p>
            <p class="mt-1 text-2xl font-bold">{{ number_format($data['blogViews']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs text-gray-500">صفحات و دسته‌ها</p>
            <p class="mt-1 text-2xl font-bold">{{ number_format($data['pageViews']) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        @include('filament.pages.partials.traffic-top-table', ['title' => 'پربازدیدترین محصولات', 'rows' => $data['topProducts']])
        @include('filament.pages.partials.traffic-top-table', ['title' => 'پربازدیدترین مقالات', 'rows' => $data['topBlogPosts']])
        @include('filament.pages.partials.traffic-top-table', ['title' => 'صفحات', 'rows' => $data['topPages']])
        @include('filament.pages.partials.traffic-top-table', ['title' => 'دسته‌ها (لیست محصول)', 'rows' => $data['topCategories']])
    </div>
</x-filament-panels::page>