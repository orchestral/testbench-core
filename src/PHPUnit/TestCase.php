<?php

namespace Orchestra\Testbench\PHPUnit;

use Orchestra\Testbench\Concerns\HandlesAssertions;
use Orchestra\Testbench\Concerns\InteractsWithMockery;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use HandlesAssertions;
    use InteractsWithMockery;

    /** {@inheritDoc} */
    #[\Override]
    protected function tearDown(): void
    {
        $this->tearDownTheTestEnvironmentUsingMockery();
    }
}
