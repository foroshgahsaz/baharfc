<?php

namespace Tests\Unit;

use App\Support\FrameworkWritablePaths;
use Tests\TestCase;

class FrameworkWritablePathsTest extends TestCase
{
    public function test_ensure_present_marks_bootstrap_cache_writable_in_dev(): void
    {
        $status = FrameworkWritablePaths::ensurePresent();

        $this->assertArrayHasKey('bootstrap/cache', $status);
        $this->assertTrue($status['bootstrap/cache']);
        $this->assertTrue(FrameworkWritablePaths::isHealthy());
    }
}