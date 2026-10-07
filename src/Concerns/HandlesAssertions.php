<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\PHPUnit\Assert;

trait HandlesAssertions
{
    /**
     * Mark the test as skipped when condition is not equivalent to true.
     *
     * @param  (\Closure(): bool)|bool  $condition
     * @param  string  $message
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function markTestSkippedUnless($condition, string $message): void
    {
        Assert::markTestSkippedUnless((value($condition ?? false)), $message);
    }

    /**
     * Mark the test as skipped when condition is equivalent to true.
     *
     * @param  (\Closure(): bool)|bool  $condition
     * @param  string  $message
     * @return void
     *
     * @codeCoverageIgnore
     */
    protected function markTestSkippedWhen($condition, string $message): void
    {
        Assert::markTestSkippedWhen((value($condition ?? false)), $message);
    }
}
