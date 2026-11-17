<?php

namespace Tests\Unit;

use App\Support\PersistentLogStorage;
use App\Support\PersistentVolume;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PersistentLogStorageTest extends TestCase
{
    public function test_ensure_logs_directory_succeeds_locally(): void
    {
        PersistentLogStorage::flushResolved();
        $result = PersistentLogStorage::ensureLogsDirectory();

        $this->assertTrue($result['ok']);
        $this->assertDirectoryExists(PersistentLogStorage::logsRoot());
    }

    public function test_log_candidates_follow_persistent_public_disk(): void
    {
        $public = '/data/baharfc/storage/app/public';
        config(['filesystems.disks.public.root' => $public]);

        $candidates = PersistentVolume::logCandidates('/data/baharfc/storage/logs');

        $this->assertContains('/data/baharfc/storage/logs', $candidates);
        $this->assertContains('/data/baharfc/storage/app/logs', $candidates);
        $this->assertContains('/data/baharfc/storage/app/public/.baharfc/logs', $candidates);
    }

    public function test_symlink_replaces_ephemeral_logs_and_keeps_files(): void
    {
        $persistent = sys_get_temp_dir().'/baharfc-persist-logs-'.uniqid('', true);
        File::makeDirectory($persistent, 0777, true);
        config(['logging.persistent_root' => $persistent]);
        PersistentLogStorage::flushResolved();

        $ephemeral = storage_path('logs');
        if (! File::isDirectory($ephemeral) && ! is_link($ephemeral)) {
            File::makeDirectory($ephemeral, 0777, true);
        }
        if (is_dir($ephemeral) && ! is_link($ephemeral)) {
            File::put($ephemeral.'/keep-me.log', 'archive');
        }

        $this->assertTrue(PersistentLogStorage::ensureStorageLogsSymlink());
        $this->assertFileExists($persistent.'/keep-me.log');
        $this->assertSame('archive', File::get($persistent.'/keep-me.log'));
        $this->assertTrue(is_link(storage_path('logs')) || realpath(storage_path('logs')) === realpath($persistent));

        $link = storage_path('logs');
        if (is_link($link)) {
            @unlink($link);
        }
        if (! File::isDirectory($link)) {
            File::makeDirectory($link, 0777, true);
        }

        File::deleteDirectory($persistent);
    }
}
