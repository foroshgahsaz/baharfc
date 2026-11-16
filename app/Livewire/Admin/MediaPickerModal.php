<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Services\Admin\MediaLibraryService;
use App\Support\SchemaGuard;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MediaPickerModal extends Component
{
    use WithPagination;

    public bool $open = false;

    public ?string $targetStatePath = null;

    public ?string $targetComponentId = null;

    public bool $imagesOnly = true;

    public string $search = '';

    public string $typeFilter = 'all';

    #[On('open-media-picker')]
    public function openPicker(string $statePath, bool $imagesOnly = true, ?string $componentId = null): void
    {
        if (! SchemaGuard::hasTable('media')) {
            return;
        }

        $this->targetStatePath = $statePath;
        $this->targetComponentId = $componentId;
        $this->imagesOnly = $imagesOnly;
        $this->typeFilter = $imagesOnly ? 'image' : 'all';
        $this->search = '';
        $this->open = true;
        $this->resetPage();
    }

    public function close(): void
    {
        $this->open = false;
        $this->targetStatePath = null;
        $this->targetComponentId = null;
    }

    public function pick(int $mediaId): void
    {
        $media = Media::query()->findOrFail($mediaId);

        if ($this->imagesOnly && ! $media->isImage()) {
            return;
        }

        $this->dispatch(
            'media-picked',
            statePath: (string) $this->targetStatePath,
            path: $media->path,
            componentId: $this->targetComponentId,
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