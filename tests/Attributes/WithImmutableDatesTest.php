<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Carbon\CarbonInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\Date;
use Orchestra\Testbench\Attributes\WithImmutableDates;
use Orchestra\Testbench\Tests\TestCase;

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

    /**
     * @test
     *
     * @depends it_uses_immutable_dates
     */
    public function it_does_not_persist_immutable_date_after_test()
    {
        $date = Date::parse('2023-01-01');

        $this->assertInstanceOf(CarbonInterface::class, $date);
        $this->assertInstanceOf(DateTimeInterface::class, $date);
        $this->assertNotInstanceOf(DateTimeImmutable::class, $date);
    }
}
