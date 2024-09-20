<?php

namespace Orchestra\Testbench\Concerns;

trait HandlesAssertions
{
    /**
     * Mark the test as skipped when condition is equivalent to true.
     *
     * @param  (\Closure($this): mixed)|mixed|null  $condition
     * @param  string  $message
     * @return void
     */
    protected function markTestSkippedWhen($condition, string $message): void
    {
        if (value($condition)) {
            $this->markTestSkipped($message);
        }
    }
}
