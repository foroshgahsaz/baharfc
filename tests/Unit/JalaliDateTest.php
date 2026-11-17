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

    public function test_parses_jalali_form_string_back_to_gregorian(): void
    {
        $carbon = JalaliDate::fromFormString('1404/08/26 14:30');

        $this->assertNotNull($carbon);
        $this->assertSame('1404/08/26 14:30', JalaliDate::dateTime($carbon));
    }
}