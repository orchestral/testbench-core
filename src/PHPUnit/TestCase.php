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
        return $error;
    }
}
