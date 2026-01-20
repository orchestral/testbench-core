<?php

namespace Orchestra\Testbench\Concerns;

use Mockery;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

trait InteractsWithMockery
{
    /**
     * Teardown the testing environment.
     *
     * @return void
     */
    protected function tearDownTheTestEnvironmentUsingMockery(): void
    {
        if (class_exists(Mockery::class) && $this instanceof PHPUnitTestCase) {
            if ($container = Mockery::getContainer()) {
                /** @var int<0, max> $expectationCount */
                $expectationCount = $container->mockery_getExpectationCount();

                $this->addToAssertionCount($expectationCount);
            }

            Mockery::close();
        }
    }
}
