<?php

namespace Tests\Unit;

use App\Services\Admin\ApplicationErrorLogReader;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ApplicationErrorLogReaderTest extends TestCase
{
    public function test_parses_and_clears_errors_for_a_day(): void
    {
        $path = storage_path('logs/laravel.log');
        File::ensureDirectoryExists(dirname($path));

        File::put($path, implode("\n", [
            '[2026-11-16 10:00:00] production.ERROR: First error {"x":1}',
            '[2026-11-16 10:01:00] production.INFO: ignored',
            '[2026-11-15 09:00:00] production.ERROR: Old error',
        ]));

        $reader = new ApplicationErrorLogReader;
        $errors = $reader->errorsForDay('2026-11-16');

        $this->assertCount(1, $errors);
        $this->assertSame('First error {"x":1}', $errors[0]['message']);

        $reader->clearDay('2026-11-16');

        $remaining = File::get($path);
        $this->assertStringNotContainsString('First error', $remaining);
        $this->assertStringContainsString('Old error', $remaining);

        File::put($path, '');
    }
}