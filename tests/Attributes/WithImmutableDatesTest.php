<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Carbon\CarbonInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;
use Orchestra\Testbench\Attributes\WithImmutableDates;
use Orchestra\Testbench\TestCase;

/**
 * @requires PHP >= 8.0
 */
class WithImmutableDatesTest extends TestCase
{
    /** @test */
    #[WithImmutableDates]
    public function it_uses_immutable_dates()
    {
        $date = Date::parse('2023-01-01');

        $this->assertInstanceOf(CarbonInterface::class, $date);
        $this->assertInstanceOf(DateTimeInterface::class, $date);
        $this->assertInstanceOf(DateTimeImmutable::class, $date);
    }
}
