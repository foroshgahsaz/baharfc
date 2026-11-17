<div>
    @if ($open ?? false)
        <div
            class="baharfc-media-picker-overlay"
            style="position:fixed;inset:0;z-index:10050;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(15,23,42,.58);"
            wire:click.self="close"
            role="dialog"
            aria-modal="true"
            aria-label="انتخاب از کتابخانه رسانه"
        >
            <div
                class="baharfc-media-picker-dialog"
                style="display:flex;flex-direction:column;width:min(1120px,100%);height:min(86vh,780px);overflow:hidden;background:#fff;border-radius:18px;"
                wire:click.stop
                dir="rtl"
            >
                <div class="baharfc-media-picker-header">
                    <h2 class="baharfc-media-picker-title">انتخاب از کتابخانه رسانه</h2>
                    <button type="button" class="baharfc-media-picker-close" wire:click="close" aria-label="بستن">&times;</button>
                </div>
                <div class="baharfc-media-picker-toolbar">
                    <input
                        type="search"
                        class="baharfc-media-picker-search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="جستجوی رسانه…"
                    >
                </div>
                <div class="baharfc-media-picker-body">
                    <div class="baharfc-media-grid">
                        @forelse ($items as $item)
                            <button
                                type="button"
                                class="baharfc-media-card"
                                wire:click="pick({{ $item->id }})"
                                title="{{ $item->title ?: $item->file_name }}"
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
                            </button>
                        @empty
                            <p class="baharfc-media-empty">رسانه‌ای یافت نشد. از منوی کتابخانهٔ رسانه آپلود کنید.</p>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $items->links() }}</div>
                </div>
            </div>
        </div>
    @endif
</div>
