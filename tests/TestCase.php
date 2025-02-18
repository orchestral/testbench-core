<?php

namespace Orchestra\Testbench\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /** {@inheritDoc} */
    protected function getApplicationBasePath()
    {
        return static::applicationBasePath();
    }
}
