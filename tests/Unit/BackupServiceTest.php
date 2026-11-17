<?php

namespace Tests\Unit;

use App\Services\Admin\BackupService;
use App\Support\PersistentBackupStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupServiceTest extends TestCase
{
    use RefreshDatabase;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = sys_get_temp_dir().'/baharfc-backups-'.uniqid('', true);
        config([
            'backup.root' => $this->root,
            'backup.keep' => 4,
            'backup.retain_days' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);
        parent::tearDown();
    }

    public function test_schema_qualified_mysql_names_are_stripped(): void
    {
        $service = app(BackupService::class);

        $this->assertSame('addresses', $service->unqualifiedTableName('bahardbxhl_db.addresses'));
        $this->assertSame('addresses', $service->unqualifiedTableName('`bahardbxhl_db`.`addresses`'));
        $this->assertSame('addresses', $service->unqualifiedTableName('addresses'));
    }

    public function test_mysql_dump_uses_native_show_tables(): void
    {
        $source = (string) file_get_contents((new \ReflectionClass(BackupService::class))->getFileName());

        $this->assertStringContainsString("query('SHOW TABLES')", $source);
        $this->assertStringContainsString('dumpMysqlWithPhp', $source);
        $this->assertStringNotContainsString('getTableListing', $source);
    }

    public function test_database_backup_writes_gzipped_sql(): void
    {
        $result = app(BackupService::class)->backupDatabase();

        $this->assertTrue($result['ok'], $result['message']);
        $this->assertFileExists($result['path']);

        $sql = gzdecode((string) file_get_contents($result['path']));
        $this->assertIsString($sql);
        $this->assertStringContainsString('INSERT INTO', $sql);
        $this->assertStringContainsString('users', $sql);
    }

    public function test_files_backup_zips_public_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('shop/logo.png', 'png-bytes');

        $result = app(BackupService::class)->backupFiles();

        $this->assertTrue($result['ok'], $result['message']);
        $this->assertFileExists($result['path']);

        $zip = new \ZipArchive;
        $this->assertTrue($zip->open($result['path']));
        $this->assertNotFalse($zip->locateName('shop/logo.png'));
        $zip->close();
    }

    public function test_prune_keeps_four_and_drops_older_than_two_days(): void
    {
        PersistentBackupStorage::ensureBackupsDirectory();
        $dir = PersistentBackupStorage::typeDirectory('database');

        for ($i = 1; $i <= 5; $i++) {
            $name = sprintf('db-2026-11-17-%06d.sql.gz', $i);
            $path = $dir.'/'.$name;
            file_put_contents($path, 'x');
            touch($path, time() - 60 * $i);
        }

        $old = $dir.'/db-2026-11-10-010101.sql.gz';
        file_put_contents($old, 'old');
        touch($old, time() - 3 * 86400);

        $deleted = app(BackupService::class)->prune('database');
        $remaining = app(BackupService::class)->list('database');

        $this->assertGreaterThanOrEqual(2, $deleted);
        $this->assertCount(4, $remaining);
        $this->assertFalse(file_exists($old));
    }
}
