<?php

namespace App\Services\Admin;

use App\Models\Media;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryService
{
    /** @var list<string> */
    public const ALLOWED_MIME_PREFIXES = [
        'image/',
        'video/',
        'audio/',
        'application/pdf',
        'application/zip',
        'text/plain',
    ];

    public function paginate(string $search = '', string $type = 'all', int $perPage = 24): LengthAwarePaginator
    {
        $query = Media::query()->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('alt', 'like', "%{$search}%");
            });
        }

        $query = match ($type) {
            'image' => $query->where('mime_type', 'like', 'image/%'),
            'video' => $query->where('mime_type', 'like', 'video/%'),
            'audio' => $query->where('mime_type', 'like', 'audio/%'),
            'document' => $query->where(function ($q): void {
                $q->where('mime_type', 'like', 'application/%')
                    ->orWhere('mime_type', 'like', 'text/%');
            }),
            default => $query,
        };

        return $query->paginate($perPage);
    }

    public function storeUploadedFile(UploadedFile $file): Media
    {
        $this->assertAllowedMime($file->getMimeType() ?? 'application/octet-stream');

        $directory = 'library/'.now()->format('Y/m');
        $storedName = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $storedName, 'public');

        $width = null;
        $height = null;
        if (Str::startsWith($file->getMimeType() ?? '', 'image/')) {
            $dimensions = @getimagesize($file->getRealPath());
            if (is_array($dimensions)) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }
        }

        $originalName = $file->getClientOriginalName();

        return Media::query()->create([
            'disk' => 'public',
            'path' => $path,
            'file_name' => $originalName,
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'title' => pathinfo($originalName, PATHINFO_FILENAME),
            'uploaded_by' => Auth::id(),
        ]);
    }

    public function updateMetadata(Media $media, array $data): Media
    {
        $media->fill([
            'title' => $data['title'] ?? $media->title,
            'alt' => $data['alt'] ?? $media->alt,
            'caption' => $data['caption'] ?? $media->caption,
            'description' => $data['description'] ?? $media->description,
        ]);
        $media->save();

        return $media->fresh();
    }

    public function delete(Media $media): void
    {
        if (Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }

        $media->delete();
    }

    /**
     * @param  list<int>  $ids
     */
    public function deleteMany(array $ids): int
    {
        $count = 0;
        foreach (Media::query()->whereIn('id', $ids)->get() as $media) {
            $this->delete($media);
            $count++;
        }

        return $count;
    }

    public function syncExistingFilesOnDisk(): int
    {
        $disk = Storage::disk('public');
        $imported = 0;

        foreach ($disk->allFiles() as $path) {
            if (str_contains($path, '.gitignore') || str_contains($path, '/.')) {
                continue;
            }

            if (Media::query()->where('disk', 'public')->where('path', $path)->exists()) {
                continue;
            }

            $mime = $disk->mimeType($path) ?? 'application/octet-stream';
            if (! $this->isAllowedMime($mime)) {
                continue;
            }

            $width = null;
            $height = null;
            if (Str::startsWith($mime, 'image/')) {
                try {
                    $dimensions = @getimagesize($disk->path($path));
                    if (is_array($dimensions)) {
                        $width = $dimensions[0] ?? null;
                        $height = $dimensions[1] ?? null;
                    }
                } catch (\Throwable) {
                }
            }

            $basename = basename($path);

            Media::query()->create([
                'disk' => 'public',
                'path' => $path,
                'file_name' => $basename,
                'mime_type' => $mime,
                'size' => $disk->size($path),
                'width' => $width,
                'height' => $height,
                'title' => pathinfo($basename, PATHINFO_FILENAME),
                'uploaded_by' => null,
                'created_at' => now()->createFromTimestamp($disk->lastModified($path)),
            ]);

            $imported++;
        }

        return $imported;
    }

    protected function assertAllowedMime(string $mime): void
    {
        if (! $this->isAllowedMime($mime)) {
            throw new \InvalidArgumentException("نوع فایل «{$mime}» مجاز نیست.");
        }
    }

    protected function isAllowedMime(string $mime): bool
    {
        foreach (self::ALLOWED_MIME_PREFIXES as $prefix) {
            if (Str::startsWith($mime, $prefix)) {
                return true;
            }
        }

        return false;
    }
}