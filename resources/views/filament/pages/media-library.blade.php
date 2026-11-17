<x-filament-panels::page>
    <div class="fi-media-library space-y-4" dir="rtl">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-wrap items-center gap-2">
                <label class="fi-btn relative inline-flex cursor-pointer items-center justify-center gap-1 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    افزودن رسانه
                    <input
                        type="file"
                        class="hidden"
                        multiple
                        accept="image/*,video/*,audio/*,application/pdf,application/zip,text/plain"
                        wire:model="newFiles"
                    >
                </label>
                <button type="button" wire:click="syncFromDisk" class="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                    همگام‌سازی با دیسک
                </button>
                <button type="button" wire:click="selectAllOnPage" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700">
                    انتخاب صفحه
                </button>
                @if (count($selectedIds) > 0)
                    <button type="button" wire:click="deleteSelected" wire:confirm="فایل‌های انتخاب‌شده برای همیشه حذف شوند؟" class="rounded-lg border border-danger-300 px-3 py-2 text-sm text-danger-600">
                        حذف انتخاب‌شده ({{ count($selectedIds) }})
                    </button>
                    <button type="button" wire:click="clearSelection" class="text-sm text-gray-500">لغو انتخاب</button>
                @endif
                <span wire:loading wire:target="newFiles" class="text-sm text-gray-500">در حال آپلود…</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="جستجو در رسانه…"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"
                >
                <select wire:model.live="typeFilter" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                    @foreach ($this->typeFilterOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button" wire:click="$set('viewMode', 'grid')" @class(['rounded-lg px-2 py-2', 'bg-primary-50 text-primary-700' => $viewMode === 'grid']) title="نمای شبکه‌ای">▦</button>
                <button type="button" wire:click="$set('viewMode', 'list')" @class(['rounded-lg px-2 py-2', 'bg-primary-50 text-primary-700' => $viewMode === 'list']) title="نمای فهرست">≡</button>
            </div>
        </div>

        @if ($viewMode === 'grid')
            <div class="baharfc-media-grid">
                @forelse ($this->mediaItems as $item)
                    <button
                        type="button"
                        class="baharfc-media-card"
                        wire:click="openDetail({{ $item->id }})"
                        @class([
                            'ring-2 ring-primary-500' => in_array($item->id, $selectedIds, true),
                        ])
                    >
                        <input
                            type="checkbox"
                            class="absolute left-2 top-2 z-10 h-4 w-4 rounded border-gray-300"
                            style="position:absolute;left:8px;top:8px;z-index:2;"
                            @checked(in_array($item->id, $selectedIds, true))
                            wire:click.stop="toggleSelection({{ $item->id }})"
                        >
                        <div class="baharfc-media-thumb">
                            @if ($item->isImage())
                                <img
                                    src="{{ $item->url() }}"
                                    alt="{{ $item->alt ?: ($item->title ?: $item->file_name) }}"
                                    loading="lazy"
                                    decoding="async"
                                    width="160"
                                    height="160"
                                    style="width:100%;height:100%;max-width:100%;max-height:100%;object-fit:cover;position:absolute;inset:0;"
                                >
                            @else
                                <span class="baharfc-media-thumb-fallback">📄</span>
                            @endif
                        </div>
                        <p class="baharfc-media-caption">{{ $item->title ?: $item->file_name }}</p>
                        <p class="baharfc-media-caption" style="padding-top:0;color:#9ca3af;">{{ $item->formattedSize() }}</p>
                    </button>
                @empty
                    <p class="baharfc-media-empty">رسانه‌ای نیست. «افزودن رسانه» را بزنید یا همگام‌سازی با دیسک.</p>
                @endforelse
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <tr>
                            <th class="w-10 p-3 text-right"></th>
                            <th class="p-3 text-right">فایل</th>
                            <th class="p-3 text-right">نوع</th>
                            <th class="p-3 text-right">تاریخ</th>
                            <th class="p-3 text-right">حجم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->mediaItems as $item)
                            <tr wire:key="media-row-{{ $item->id }}" class="border-b border-gray-50 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/30">
                                <td class="p-3">
                                    <input type="checkbox" @checked(in_array($item->id, $selectedIds, true)) wire:click="toggleSelection({{ $item->id }})">
                                </td>
                                <td class="p-3">
                                    <button type="button" wire:click="openDetail({{ $item->id }})" class="flex items-center gap-2 text-right">
                                        @if ($item->isImage())
                                            <img src="{{ $item->url() }}" alt="" width="40" height="40" style="width:40px;height:40px;object-fit:cover;border-radius:8px;flex-shrink:0;" loading="lazy">
                                        @endif
                                        <span class="font-medium">{{ $item->title ?: $item->file_name }}</span>
                                    </button>
                                </td>
                                <td class="p-3">{{ $item->typeLabel() }}</td>
                                <td class="p-3">{{ \App\Support\JalaliDate::dateTime($item->created_at) }}</td>
                                <td class="p-3">{{ $item->formattedSize() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500">رسانه‌ای یافت نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div>
            {{ $this->mediaItems->links() }}
        </div>

        @if ($this->detailMedia)
            <div class="fixed inset-0 z-50 flex justify-end bg-black/40" wire:click.self="closeDetail">
                <div class="flex h-full w-full max-w-md flex-col overflow-y-auto bg-white shadow-xl dark:bg-gray-900" wire:click.stop>
                    <div class="flex items-center justify-between border-b border-gray-100 p-4 dark:border-gray-800">
                        <h3 class="font-semibold">جزئیات پیوست</h3>
                        <button type="button" wire:click="closeDetail" class="text-2xl leading-none text-gray-500">&times;</button>
                    </div>
                    <div class="p-4">
                        @if ($this->detailMedia->isImage())
                            <img src="{{ $this->detailMedia->url() }}" alt="" class="mb-4 max-h-56 w-full rounded-lg bg-gray-50 object-contain" style="max-height:14rem;width:100%;object-fit:contain;">
                        @endif
                        <p class="mb-1 text-xs text-gray-500">آدرس فایل</p>
                        <p class="mb-4 break-all rounded-lg bg-gray-50 p-2 text-xs dark:bg-gray-800" dir="ltr">{{ $this->detailMedia->url() }}</p>
                        <button
                            type="button"
                            x-data
                            x-on:click="navigator.clipboard.writeText(@js($this->detailMedia->url()))"
                            class="mb-4 w-full rounded-lg border border-gray-200 py-2 text-sm dark:border-gray-700"
                        >
                            کپی آدرس
                        </button>
                        <label class="mb-2 block text-sm font-medium">عنوان</label>
                        <input type="text" wire:model="editTitle" class="mb-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <label class="mb-2 block text-sm font-medium">متن جایگزین (Alt)</label>
                        <input type="text" wire:model="editAlt" class="mb-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <label class="mb-2 block text-sm font-medium">زیرنویس</label>
                        <input type="text" wire:model="editCaption" class="mb-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <label class="mb-2 block text-sm font-medium">توضیح</label>
                        <textarea wire:model="editDescription" rows="3" class="mb-4 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                        <dl class="mb-4 grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-400">
                            <div><dt>نام فایل</dt><dd>{{ $this->detailMedia->file_name }}</dd></div>
                            <div><dt>نوع</dt><dd>{{ $this->detailMedia->mime_type }}</dd></div>
                            <div><dt>حجم</dt><dd>{{ $this->detailMedia->formattedSize() }}</dd></div>
                            @if ($this->detailMedia->width)
                                <div><dt>ابعاد</dt><dd>{{ $this->detailMedia->width }}×{{ $this->detailMedia->height }}</dd></div>
                            @endif
                        </dl>
                        <button wire:click="saveMetadata" type="button" class="mb-2 w-full rounded-lg bg-primary-600 py-2.5 text-sm font-semibold text-white hover:bg-primary-500">ذخیره</button>
                        <button wire:click="deleteDetail" wire:confirm="این فایل برای همیشه حذف شود؟" type="button" class="w-full rounded-lg border border-danger-300 py-2.5 text-sm text-danger-600">حذف دائمی</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>