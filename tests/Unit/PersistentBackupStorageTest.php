<?php

namespace Tests\Unit;

use App\Support\PersistentBackupStorage;
use App\Support\PersistentVolume;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PersistentBackupStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        PersistentBackupStorage::flushResolved();
    }

    public function test_ensure_backups_directory_uses_configured_root(): void
    {
        $root = sys_get_temp_dir().'/baharfc-backup-root-'.uniqid('', true);
        config(['backup.root' => $root]);
        PersistentBackupStorage::flushResolved();

        $result = PersistentBackupStorage::ensureBackupsDirectory();

        $this->assertTrue($result['ok'], $result['message']);
        $this->assertDirectoryExists($root.'/database');
        $this->assertDirectoryExists($root.'/files');

        File::deleteDirectory($root);
    }

    public function test_falls_back_next_to_persistent_public_disk(): void
    {
        $base = sys_get_temp_dir().'/baharfc-vol-'.uniqid('', true);
        $public = $base.'/app/public';
        File::makeDirectory($public, 0777, true);

        config([
            'backup.root' => '/proc/1/baharfc-not-writable-'.uniqid('', true),
            'filesystems.disks.public.root' => $public,
        ]);
        PersistentBackupStorage::flushResolved();

        $result = PersistentBackupStorage::ensureBackupsDirectory();

        $this->assertTrue($result['ok'], $result['message']);
        $this->assertSame($base.'/app/backups', PersistentBackupStorage::backupsRoot());
        $this->assertDirectoryExists($base.'/app/backups/database');

        File::deleteDirectory($base);
    }

    public function test_backup_candidates_include_public_sibling(): void
    {
        $candidates = PersistentVolume::backupCandidates('/data/baharfc/storage/backups');

        $this->assertContains('/data/baharfc/storage/backups', $candidates);
    }
}
