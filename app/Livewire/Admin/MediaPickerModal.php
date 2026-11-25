<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Services\Admin\MediaLibraryService;
use App\Support\SchemaGuard;
use Filament\Notifications\Notification;
use Illuminate\Support\Js;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class MediaPickerModal extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $open = false;

    public ?string $targetStatePath = null;

    public ?string $targetComponentId = null;

    public bool $imagesOnly = true;

    public bool $multiSelect = false;

    /** @var list<int> */
    public array $selectedIds = [];

    public string $search = '';

    public string $typeFilter = 'all';

    /** @var array<int, TemporaryUploadedFile> */
    public array $newFiles = [];

    #[On('open-media-picker')]
    public function openPicker(mixed $statePath = null, mixed $imagesOnly = true, mixed $componentId = null, mixed $multiple = false): void
    {
        if (is_array($statePath)) {
            $payload = $statePath;
            $statePath = $payload['statePath'] ?? null;
            $imagesOnly = $payload['imagesOnly'] ?? true;
            $componentId = $payload['componentId'] ?? null;
            $multiple = $payload['multiple'] ?? false;
        }

        if (! is_string($statePath) || $statePath === '') {
            return;
        }

        if (! SchemaGuard::hasTable('media')) {
            Notification::make()
                ->title('جدول رسانه وجود ندارد')
                ->body('روی سرور: php artisan migrate --force')
                ->warning()
                ->send();

            return;
        }

        $this->targetStatePath = $statePath;
        $this->targetComponentId = is_string($componentId) ? $componentId : null;
        $this->imagesOnly = filter_var($imagesOnly, FILTER_VALIDATE_BOOLEAN);
        $this->multiSelect = filter_var($multiple, FILTER_VALIDATE_BOOLEAN);
        $this->selectedIds = [];
        $this->typeFilter = $imagesOnly ? 'image' : 'all';
        $this->search = '';
        $this->newFiles = [];
        $this->open = true;
        $this->resetPage();
    }

    public function close(): void
    {
        $this->open = false;
        $this->targetStatePath = null;
        $this->targetComponentId = null;
        $this->multiSelect = false;
        $this->selectedIds = [];
        $this->newFiles = [];
    }

    public function updatedNewFiles(): void
    {
        if ($this->newFiles === []) {
            return;
        }

        $this->uploadNewFiles();
    }

    public function uploadNewFiles(): void
    {
        if (! $this->open) {
            $this->newFiles = [];

            return;
        }

        $service = app(MediaLibraryService::class);
        $uploaded = [];

        try {
            foreach ($this->newFiles as $file) {
                if (! $file instanceof TemporaryUploadedFile) {
                    continue;
                }

                $mime = (string) ($file->getMimeType() ?? '');
                if ($this->imagesOnly && ! str_starts_with($mime, 'image/')) {
                    Notification::make()
                        ->title('فقط فایل تصویری مجاز است')
                        ->warning()
                        ->send();

                    continue;
                }

                $uploaded[] = $service->storeUploadedFile($file);
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('آپلود ناموفق')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->newFiles = [];
            $this->resetPage();
        }

        if (count($uploaded) === 1) {
            $this->pick($uploaded[0]->id);

            return;
        }

        if (count($uploaded) > 1) {
            Notification::make()
                ->title(count($uploaded).' فایل به کتابخانه اضافه شد — یکی را انتخاب کنید')
                ->success()
                ->send();
        }
    }

    public function toggleSelect(int $mediaId): void
    {
        $media = Media::query()->findOrFail($mediaId);

        if ($this->imagesOnly && ! $media->isImage()) {
            return;
        }

        if (in_array($mediaId, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_filter(
                $this->selectedIds,
                fn (int $id) => $id !== $mediaId,
            ));
        } else {
            $this->selectedIds[] = $mediaId;
        }
    }

    public function confirmMultiSelect(): void
    {
        if ($this->selectedIds === []) {
            Notification::make()
                ->title('حداقل یک تصویر انتخاب کنید')
                ->warning()
                ->send();

            return;
        }

        $paths = Media::query()
            ->whereIn('id', $this->selectedIds)
            ->get()
            ->sortBy(fn (Media $media) => array_search($media->id, $this->selectedIds, true))
            ->pluck('path')
            ->filter()
            ->values()
            ->all();

        $statePath = (string) $this->targetStatePath;
        $componentId = $this->targetComponentId;

        $this->js(sprintf(
            'window.baharfcApplyMediaPickMultiple?.(%s, %s, %s)',
            Js::from($statePath),
            Js::from($paths),
            Js::from($componentId),
        ));

        $this->dispatch(
            'media-multi-picked',
            statePath: $statePath,
            paths: $paths,
            componentId: $componentId,
        );

        $this->close();
    }

    public function pick(int $mediaId): void
    {
        if ($this->multiSelect) {
            $this->toggleSelect($mediaId);

            return;
        }

        $media = Media::query()->findOrFail($mediaId);

        if ($this->imagesOnly && ! $media->isImage()) {
            return;
        }

        $statePath = (string) $this->targetStatePath;
        $path = $media->path;
        $componentId = $this->targetComponentId;

        $this->js(sprintf(
            'window.baharfcApplyMediaPick?.(%s, %s, %s)',
            Js::from($statePath),
            Js::from($path),
            Js::from($componentId),
        ));

        $this->dispatch(
            'media-picked',
            statePath: $statePath,
            path: $path,
            componentId: $componentId,
        );

        $this->close();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        if (! $this->open || ! SchemaGuard::hasTable('media')) {
            return view('livewire.admin.media-picker-modal', [
                'open' => false,
                'items' => collect(),
            ]);
        }

        $type = $this->imagesOnly ? 'image' : $this->typeFilter;

        return view('livewire.admin.media-picker-modal', [
            'open' => true,
            'multiSelect' => $this->multiSelect,
            'selectedIds' => $this->selectedIds,
            'items' => app(MediaLibraryService::class)->paginate(
                search: trim($this->search),
                type: $type,
                perPage: 18,
            ),
        ]);
    }
}
