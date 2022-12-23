<?php

namespace Orchestra\Testbench\Concerns;

use Throwable;

if (! class_exists(\PHPUnit\Runner\Version::class)) {
    return;
}

if (\intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) === 1) {
    trait HandlesNotSuccessfulTests
    {
        /**
         * This method is called when a test method did not execute successfully.
         *
         * @param  \Throwable  $exception
         * @return never
         */
        protected function onNotSuccessfulTest(Throwable $exception): never
        {
            parent::onNotSuccessfulTest(
                ! \is_null(static::$latestResponse)
                    ? static::$latestResponse->transformNotSuccessfulException($exception)
                    : $exception
            );
        }
    }
} else {
    trait HandlesNotSuccessfulTests
    {
        /**
         * This method is called when a test method did not execute successfully.
         *
         * @param  \Throwable  $exception
         * @return void
         */
        protected function onNotSuccessfulTest(Throwable $exception): void
        {
            parent::onNotSuccessfulTest(
                ! \is_null(static::$latestResponse)
                    ? static::$latestResponse->transformNotSuccessfulException($exception)
                    : $exception
            );
        }
    }
}
