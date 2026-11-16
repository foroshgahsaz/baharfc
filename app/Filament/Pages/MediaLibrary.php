<?php

namespace App\Filament\Pages;

use App\Models\Media;
use App\Services\Admin\MediaLibraryService;
use App\Support\SchemaGuard;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;
use UnitEnum;

class MediaLibrary extends Page
{
    use WithFileUploads;
    use WithPagination;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'کتابخانهٔ رسانه';

    protected static ?string $title = 'کتابخانهٔ رسانه';

    protected static string|UnitEnum|null $navigationGroup = 'مدیریت محتوا';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'media';

    protected string $view = 'filament.pages.media-library';

    protected $paginationTheme = 'tailwind';

    public string $search = '';

    public string $typeFilter = 'all';

    public string $viewMode = 'grid';

    /** @var list<int> */
    public array $selectedIds = [];

    public ?int $detailId = null;

    public ?string $editTitle = null;

    public ?string $editAlt = null;

    public ?string $editCaption = null;

    public ?string $editDescription = null;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $newFiles = [];

    public function mount(MediaLibraryService $service): void
    {
        if (Media::query()->count() === 0) {
            $imported = $service->syncExistingFilesOnDisk();
            if ($imported > 0) {
                Notification::make()
                    ->title("{$imported} فایل قدیمی از دیسک به کتابخانه اضافه شد")
                    ->info()
                    ->send();
            }
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return SchemaGuard::hasTable('media');
    }

    public static function canAccess(): bool
    {
        if (! SchemaGuard::hasTable('media')) {
            return false;
        }

        return auth()->user()?->canAdmin('media.view') ?? false;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function getMediaItemsProperty(): LengthAwarePaginator
    {
        return app(MediaLibraryService::class)->paginate(
            search: trim($this->search),
            type: $this->typeFilter,
        );
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
        $service = app(MediaLibraryService::class);
        $count = 0;

        try {
            foreach ($this->newFiles as $file) {
                $service->storeUploadedFile($file);
                $count++;
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('آپلود ناموفق')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return;
        } finally {
            $this->newFiles = [];
            $this->resetPage();
        }

        Notification::make()
            ->title($count > 0 ? "{$count} فایل افزوده شد" : 'فایلی آپلود نشد')
            ->success()
            ->send();
    }

    public function syncFromDisk(): void
    {
        $imported = app(MediaLibraryService::class)->syncExistingFilesOnDisk();
        $this->resetPage();

        Notification::make()
            ->title($imported > 0 ? "{$imported} فایل جدید شناسایی شد" : 'فایل جدیدی روی دیسک نبود')
            ->success()
            ->send();
    }

    public function toggleSelection(int $id): void
    {
        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, [$id]));
        } else {
            $this->selectedIds[] = $id;
        }
    }

    public function selectAllOnPage(): void
    {
        $this->selectedIds = $this->mediaItems->getCollection()->pluck('id')->all();
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function openDetail(int $id): void
    {
        $media = Media::query()->findOrFail($id);
        $this->detailId = $id;
        $this->editTitle = $media->title ?? '';
        $this->editAlt = $media->alt ?? '';
        $this->editCaption = $media->caption ?? '';
        $this->editDescription = $media->description ?? '';
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
    }

    public function saveMetadata(): void
    {
        if ($this->detailId === null) {
            return;
        }

        $media = Media::query()->findOrFail($this->detailId);
        app(MediaLibraryService::class)->updateMetadata($media, [
            'title' => $this->editTitle,
            'alt' => $this->altText($this->editAlt),
            'caption' => $this->editCaption,
            'description' => $this->editDescription,
        ]);

        Notification::make()->title('اطلاعات فایل ذخیره شد')->success()->send();
    }

    public function deleteDetail(): void
    {
        if ($this->detailId === null) {
            return;
        }

        $media = Media::query()->findOrFail($this->detailId);
        app(MediaLibraryService::class)->delete($media);
        $this->selectedIds = array_values(array_diff($this->selectedIds, [$this->detailId]));
        $this->closeDetail();
        $this->resetPage();

        Notification::make()->title('فایل حذف شد')->success()->send();
    }

    public function deleteSelected(): void
    {
        if ($this->selectedIds === []) {
            return;
        }

        $deleted = app(MediaLibraryService::class)->deleteMany($this->selectedIds);
        $this->selectedIds = [];
        $this->closeDetail();
        $this->resetPage();

        Notification::make()
            ->title("{$deleted} فایل حذف شد")
            ->success()
            ->send();
    }

    public function getDetailMediaProperty(): ?Media
    {
        if ($this->detailId === null) {
            return null;
        }

        return Media::query()->find($this->detailId);
    }

    public function getTypeFilterOptionsProperty(): Collection
    {
        return collect([
            'all' => 'همه',
            'image' => 'تصاویر',
            'video' => 'ویدیو',
            'audio' => 'صوت',
            'document' => 'اسناد',
        ]);
    }

    protected function altText(?string $value): ?string
    {
        return filled($value) ? $value : null;
    }
}