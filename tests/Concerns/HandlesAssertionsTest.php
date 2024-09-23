<?php

namespace Orchestra\Testbench\Tests\Concerns;

use Orchestra\Testbench\Concerns\HandlesAssertions;
use PHPUnit\Framework\TestCase;

class HandlesAssertionsTest extends TestCase
{
    use HandlesAssertions;

    /** @test */
    public function it_should_mark_the_tests_as_skipped_when_condition_is_true()
    {
        $this->markTestSkippedWhen(true, 'Successfully skipped current test');

        $this->assertTrue(false, 'Test incorrectly executed.');
    }

    /** @test */
    public function it_should_mark_the_tests_as_skipped_when_condition_is_false()
    {
        $this->markTestSkippedWhen(function () {
            return false;
        }, 'Failed skipped current test');

        $this->assertTrue(true, 'Test is correctly executed.');
    }

    /** @test */
    public function it_should_mark_the_tests_as_skipped_unless_condition_is_false()
    {
        $this->markTestSkippedUnless(false, 'Successfully skipped current test');

        $this->assertTrue(false, 'Test incorrectly executed.');
    }

    /** @test */
    public function it_should_mark_the_tests_as_skipped_unless_condition_is_true()
    {
        $this->markTestSkippedUnless(function () {
            return true;
        }, 'Failed skipped current test');

        $this->assertTrue(true, 'Test is correctly executed.');
    }
}
