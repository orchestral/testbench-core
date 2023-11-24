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
        /** @var \Illuminate\Testing\TestResponse|null $response */
        $response = static::$latestResponse ?? null;

        if (! \is_null($response)) {
            $response->transformNotSuccessfulException($error);
        }

        return $error;
    }
}
