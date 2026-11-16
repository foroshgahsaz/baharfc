<?php

namespace Tests\Unit;

use App\Support\PublicUploadStorage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicUploadStorageTest extends TestCase
{
    public function test_ensure_upload_directories_on_fresh_root(): void
    {
        $root = storage_path('framework/testing/public-upload-'.uniqid());
        Config::set('filesystems.disks.public.root', $root);

        $result = PublicUploadStorage::ensureUploadDirectories();

        $this->assertTrue($result['ok'], $result['message']);
        $this->assertDirectoryExists($root.'/shop');
        $this->assertTrue(Storage::disk('public')->exists('shop'));

        foreach (PublicUploadStorage::uploadSubdirectories() as $dir) {
            $this->assertDirectoryExists($root.'/'.str_replace('/', DIRECTORY_SEPARATOR, $dir));
        }
    }
}