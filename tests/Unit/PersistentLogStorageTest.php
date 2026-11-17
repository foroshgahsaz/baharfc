<?php

namespace Tests\Unit;

use App\Support\PersistentLogStorage;
use Tests\TestCase;

class PersistentLogStorageTest extends TestCase
{
    public function test_ensure_logs_directory_succeeds_locally(): void
    {
        $result = PersistentLogStorage::ensureLogsDirectory();

        $this->assertTrue($result['ok']);
        $this->assertDirectoryExists(PersistentLogStorage::logsRoot());
    }
}