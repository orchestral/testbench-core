<?php

namespace Orchestra\Testbench\Concerns;

trait HandlesAssertions
{
    /**
     * Mark the test as skipped when condition is not equivalent to true.
     *
     * @param  (\Closure($this): bool)|bool|null  $condition
     * @param  string  $message
     * @return void
     */
    protected function markTestSkippedUnless($condition, string $message): void
    {
        /** @phpstan-ignore argument.type */
        if (! value($condition)) {
            $this->markTestSkipped($message);
        }
    }

    /**
     * Mark the test as skipped when condition is equivalent to true.
     *
     * @param  (\Closure($this): bool)|bool|null  $condition
     * @param  string  $message
     * @return void
     */
    protected function markTestSkippedWhen($condition, string $message): void
    {
        /** @phpstan-ignore argument.type */
        if (value($condition)) {
            $this->markTestSkipped($message);
        }
    }
}
