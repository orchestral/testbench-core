<?php

namespace Orchestra\Testbench\Concerns;

use Pest\Contracts\HasPrintableTestCaseName;

trait InteractsWithPest
{
    use InteractsWithPHPUnit;

    /**
     * Determine if the trait is used within testing using Pest.
     *
     * @return bool
     */
    public function isRunningTestCaseUsingPest(): bool
    {
        /** @phpstan-ignore-next-line */
        return $this->isRunningTestCase() && isset(class_implements($this)[HasPrintableTestCaseName::class]);
    }
}
