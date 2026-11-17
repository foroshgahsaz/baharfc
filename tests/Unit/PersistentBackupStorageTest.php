<?php

namespace Tests\Unit;

use App\Support\PersistentBackupStorage;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PersistentBackupStorageTest extends TestCase
{
    public function test_ensure_backups_directory_uses_configured_root(): void
    {
        $root = sys_get_temp_dir().'/baharfc-backup-root-'.uniqid('', true);
        config(['backup.root' => $root]);

        $result = PersistentBackupStorage::ensureBackupsDirectory();

        $this->assertTrue($result['ok']);
        $this->assertDirectoryExists($root.'/database');
        $this->assertDirectoryExists($root.'/files');

        File::deleteDirectory($root);
    }
}
