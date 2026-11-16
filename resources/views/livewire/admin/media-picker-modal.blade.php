<div>
    @if ($open ?? false)
        <div class="fixed inset-0 z-[120] flex items-center justify-center bg-black/50 p-4" wire:click.self="close">
            <div class="flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900" wire:click.stop dir="rtl">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                    <h2 class="text-lg font-bold">انتخاب از کتابخانه رسانه</h2>
                    <button type="button" wire:click="close" class="text-2xl leading-none text-gray-500">&times;</button>
                </div>
                <div class="border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="جستجوی رسانه…"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"
                    >
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6">
                        @forelse ($items as $item)
                            <button
                                type="button"
                                wire:click="pick({{ $item->id }})"
                                class="overflow-hidden rounded-xl border border-gray-200 text-right transition hover:ring-2 hover:ring-primary-500 dark:border-gray-700"
                            >
                                <div class="flex aspect-square items-center justify-center bg-gray-50 dark:bg-gray-800">
                                    @if ($item->isImage())
                                        <img src="{{ $item->url() }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <span class="text-2xl">📄</span>
                                    @endif
                                </div>
                                <p class="truncate p-2 text-[11px]">{{ $item->title ?: $item->file_name }}</p>
                            </button>
                        @empty
                            <p class="col-span-full py-8 text-center text-sm text-gray-500">رسانه‌ای یافت نشد. از منوی کتابخانهٔ رسانه آپلود کنید.</p>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $items->links() }}</div>
                </div>
            </div>
        </div>
    @endif
</div>