<?php

namespace Orchestra\Testbench\PHPUnit;

use Throwable;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function transformException(Throwable $error): Throwable
    {
        /** @phpstan-ignore-next-line */
        if (isset(static::$latestResponse)) {
            if (! \is_null(static::$latestResponse)) {
                static::$latestResponse->transformNotSuccessfulException($error);
            }
        }

        return $error;
    }
}
