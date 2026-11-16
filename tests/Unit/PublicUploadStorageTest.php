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

    public function test_ensure_public_symlink_creates_link(): void
    {
        $root = storage_path('framework/testing/symlink-target-'.uniqid());
        Config::set('filesystems.disks.public.root', $root);
        Config::set('filesystems.links', [public_path('storage') => $root]);

        $link = public_path('storage');
        $backup = null;
        if (file_exists($link)) {
            $backup = $link.'_symlink_test_'.uniqid();
            rename($link, $backup);
        }

        try {
            $this->assertTrue(PublicUploadStorage::ensurePublicSymlink());
            $this->assertTrue(PublicUploadStorage::symlinkDiagnostics()['ok']);
        } finally {
            if (file_exists($link)) {
                if (is_link($link)) {
                    unlink($link);
                }
            }
            if ($backup !== null && file_exists($backup)) {
                rename($backup, $link);
            }
        }
    }

    public function test_symlink_diagnostics_detects_missing_public_storage_link(): void
    {
        $link = public_path('storage');
        $backup = null;
        if (file_exists($link)) {
            $backup = $link.'_test_backup_'.uniqid();
            rename($link, $backup);
        }

        try {
            $diagnostics = PublicUploadStorage::symlinkDiagnostics();
            $this->assertFalse($diagnostics['ok']);
        } finally {
            if ($backup !== null && file_exists($backup)) {
                rename($backup, $link);
            }
        }
    }
}