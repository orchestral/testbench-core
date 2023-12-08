<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Pest\Hook;
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

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function setUpBeforeClassUsingPest(): void
    {
        //
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function tearDownAfterClassUsingPest(): void
    {
        if (class_exists(Hook::class)) {
            Hook::flush();
        }
    }
}
