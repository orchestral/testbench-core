<?php

namespace Orchestra\Testbench\PHPUnit;

use function Orchestra\Testbench\phpunit_version_compare;
use Throwable;

if (phpunit_version_compare('10.1.0', '<')) {
    class TestCase extends \PHPUnit\Framework\TestCase
    {
        /**
         * {@inheritdoc}
         */
        protected function onNotSuccessfulTest(Throwable $exception): never
        {
            /** @var \Illuminate\Testing\TestResponse|null $response */
            $response = static::$latestResponse ?? null;

            parent::onNotSuccessfulTest(
                \is_null($response)
                    ? $response->transformNotSuccessfulException($exception)
                    : $exception
            );
        }
    }
} elseif (phpunit_version_compare('10.0.0', '<')) {
    class TestCase extends \PHPUnit\Framework\TestCase
    {
        /**
         * {@inheritdoc}
         */
        protected function onNotSuccessfulTest(Throwable $exception): void
        {
            /** @var \Illuminate\Testing\TestResponse|null $response */
            $response = static::$latestResponse ?? null;

            parent::onNotSuccessfulTest(
                \is_null($response)
                    ? $response->transformNotSuccessfulException($exception)
                    : $exception
            );
        }
    }
} else {
    class TestCase extends \PHPUnit\Framework\TestCase
    {
        /**
         * {@inheritdoc}
         */
        protected function transformException(Throwable $error): Throwable
        {
            /** @var \Illuminate\Testing\TestResponse|null $response */
            $response = static::$latestResponse ?? null;

            if (! \is_null($response)) {
                $response->transformNotSuccessfulException($error);
            }

            return $error;
        }
    }
}
