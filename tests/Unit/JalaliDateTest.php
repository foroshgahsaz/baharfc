<?php

namespace Tests\Unit;

use App\Support\JalaliDate;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JalaliDateTest extends TestCase
{
    public function test_formats_gregorian_march_to_shamsi(): void
    {
        $formatted = JalaliDate::date(Carbon::parse('2026-03-21 12:00:00'));

        $this->assertSame('1405/01/01', $formatted);
    }
}