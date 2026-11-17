@php
    $statePath = $getStatePath();
    $state = $getState();
    $livewireId = $field->getLivewire()->getId();
    $previewUrl = null;
    if (filled($state) && \Illuminate\Support\Facades\Storage::disk('public')->exists($state)) {
        $previewUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($state);
    }
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="space-y-3">
        @if ($previewUrl && \Illuminate\Support\Str::startsWith((string) (\Illuminate\Support\Facades\Storage::disk('public')->mimeType($state) ?? ''), 'image/'))
            <img src="{{ $previewUrl }}" alt="" class="max-h-36 rounded-lg border border-gray-200 object-contain dark:border-gray-700">
        @elseif (filled($state))
            <p class="truncate text-sm text-gray-600 dark:text-gray-300" dir="ltr">{{ $state }}</p>
        @endif

        <div class="flex flex-wrap gap-2">
            <x-filament::button
                type="button"
                size="sm"
                color="primary"
                wire:click="$dispatchTo('admin.media-picker-modal', 'open-media-picker', { statePath: @js($statePath), imagesOnly: @js($field->acceptsImagesOnly()), componentId: @js($livewireId) })"
            >
                انتخاب از کتابخانه
            </x-filament::button>
            @if (filled($state))
                <x-filament::button type="button" size="sm" color="gray" wire:click="$set('{{ $statePath }}', null)">
                    حذف
                </x-filament::button>
            @endif
        </div>
    </div>
</x-dynamic-component>