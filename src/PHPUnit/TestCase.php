<?php

namespace Orchestra\Testbench\PHPUnit;

use Throwable;
use function Orchestra\Testbench\phpunit_version_compare;

if (phpunit_version_compare('10.1.0', '>=')) {
    class TestCase extends \PHPUnit\Framework\TestCase {
        /**
         * {@inheritdoc}
         */
        protected function transformException(Throwable $error): Throwable
        {
            if (! \is_null(static::$latestResponse)) {
                static::$latestResponse->transformNotSuccessfulException($error);
            }

            return $error;
        }
    }
} else {
    class TestCase extends \PHPUnit\Framework\TestCase {
        /**
         * {@inheritdoc}
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
