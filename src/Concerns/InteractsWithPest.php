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
        return $this->isRunningTestCase() && isset(class_implements($this)[HasPrintableTestCaseName::class]);
    }
}
