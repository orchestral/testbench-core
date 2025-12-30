<?php

namespace Orchestra\Testbench\Concerns;

use Pest\Contracts\HasPrintableTestCaseName;

trait InteractsWithPest
{
    use InteractsWithPHPUnit;
    use InteractsWithTestCase;

    /**
     * Determine if the trait is used within testing using Pest.
     *
     * @return bool
     */
    public function isRunningTestCaseUsingPest(): bool
    {
        /** @phpstan-ignore-next-line */
        return $this->isRunningTestCase() && static::isRunningViaPestPrinter($this);
    }

    /**
     * Determine if the object implements Pest test runner.
     *
     * @return bool
     */
    protected static function isRunningViaPestPrinter(object|string $object): bool
    {
        return isset(class_implements($object, false)[HasPrintableTestCaseName::class]);
    }
}
