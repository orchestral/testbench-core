<?php

namespace Orchestra\Testbench\PHPUnit;

use Orchestra\Testbench\Exceptions\DeprecatedException;
use Throwable;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function runTest(): mixed
    {
        $result = null;

        /** @var \Illuminate\Testing\TestResponse|null $response */
        $response = static::$latestResponse ?? null;

        try {
            $result = parent::runTest();
        } catch (DeprecatedException $error) {
            throw $error;
        } catch (Throwable $error) {
            if (! \is_null($response)) {
                $response->transformNotSuccessfulException($error);
            }

            throw $error;
        }

        return $result;
    }
}
