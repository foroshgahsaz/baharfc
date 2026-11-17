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

    public string $search = '';

    public string $typeFilter = 'all';

    /** @var array<int, TemporaryUploadedFile> */
    public array $newFiles = [];

    #[On('open-media-picker')]
    public function openPicker(mixed $statePath = null, mixed $imagesOnly = true, mixed $componentId = null): void
    {
        if (is_array($statePath)) {
            $payload = $statePath;
            $statePath = $payload['statePath'] ?? null;
            $imagesOnly = $payload['imagesOnly'] ?? true;
            $componentId = $payload['componentId'] ?? null;
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

    public function pick(int $mediaId): void
    {
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
            'items' => app(MediaLibraryService::class)->paginate(
                search: trim($this->search),
                type: $type,
                perPage: 18,
            ),
        ]);
    }
}