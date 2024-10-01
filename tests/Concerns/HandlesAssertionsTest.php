<?php

namespace Orchestra\Testbench\Tests\Concerns;

use Orchestra\Testbench\Concerns\HandlesAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HandlesAssertionsTest extends TestCase
{
    use HandlesAssertions;

    #[Test]
    public function it_should_mark_the_tests_as_skipped_when_condition_is_true()
    {
        $this->markTestSkippedWhen(true, 'Successfully skipped current test');

        $this->assertTrue(false, 'Test incorrectly executed.');
    }

    #[Test]
    public function it_should_mark_the_tests_as_skipped_when_condition_is_false()
    {
        $this->markTestSkippedWhen(function () {
            return false;
        }, 'Failed skipped current test');

        $this->assertTrue(true, 'Test is correctly executed.');
    }

    #[Test]
    public function it_should_mark_the_tests_as_skipped_unless_condition_is_false()
    {
        $this->markTestSkippedUnless(false, 'Successfully skipped current test');

        $this->assertTrue(false, 'Test incorrectly executed.');
    }

    #[Test]
    public function it_should_mark_the_tests_as_skipped_unless_condition_is_true()
    {
        $this->markTestSkippedUnless(function () {
            return true;
        }, 'Failed skipped current test');

        $this->assertTrue(true, 'Test is correctly executed.');
    }
}
