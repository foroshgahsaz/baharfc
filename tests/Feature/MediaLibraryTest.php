<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\Admin\MediaLibraryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_media_library_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/media')
            ->assertSuccessful();
    }

    public function test_store_upload_creates_media_record(): void
    {
        Storage::fake('public');

        $service = app(MediaLibraryService::class);
        $file = UploadedFile::fake()->image('logo.png', 120, 80);

        $media = $service->storeUploadedFile($file);

        $this->assertInstanceOf(Media::class, $media);
        Storage::disk('public')->assertExists($media->path);
        $this->assertSame(120, $media->width);
    }

    public function test_sync_imports_existing_disk_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/test.jpg', 'fake-image-content');

        $imported = app(MediaLibraryService::class)->syncExistingFilesOnDisk();

        $this->assertGreaterThanOrEqual(1, $imported);
        $this->assertTrue(Media::query()->where('path', 'products/test.jpg')->exists());
    }
}