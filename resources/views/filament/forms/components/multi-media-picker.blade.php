@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $statePath = $getStatePath();
    $paths = array_values(array_filter((array) ($getState() ?? [])));
    $livewireId = $field->getLivewire()->getId();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="space-y-3">
        @if ($paths !== [])
            <div class="flex flex-wrap gap-2">
                @foreach ($paths as $index => $path)
                    @php
                        $previewUrl = filled($path) && Storage::disk('public')->exists($path)
                            ? Storage::disk('public')->url($path)
                            : null;
                    @endphp
                    <div class="relative rounded-lg border border-gray-200 p-1 dark:border-gray-700">
                        @if ($previewUrl && Str::startsWith((string) (Storage::disk('public')->mimeType($path) ?? ''), 'image/'))
                            <img src="{{ $previewUrl }}" alt="" class="size-20 rounded-md object-cover">
                        @else
                            <span class="flex size-20 items-center justify-center text-xs text-gray-500" dir="ltr">{{ $path }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                class="fi-btn fi-btn-size-sm fi-color fi-color-primary fi-btn-color-primary"
                data-baharfc-media-picker-open="1"
                data-state-path="{{ $statePath }}"
                data-component-id="{{ $livewireId }}"
                data-images-only="{{ $field->acceptsImagesOnly() ? '1' : '0' }}"
                data-multiple="1"
            >
                انتخاب چند تصویر از کتابخانه
            </button>
            @if ($paths !== [])
                <x-filament::button type="button" size="sm" color="gray" wire:click="$set('{{ $statePath }}', [])">
                    پاک کردن همه
                </x-filament::button>
            @endif
        </div>
    </div>
</x-dynamic-component>