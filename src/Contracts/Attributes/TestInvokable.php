<?php

namespace Orchestra\Testbench\Contracts\Attributes;

interface TestInvokable extends TestingFeature
{
    /**
     * Handle the attribute.
     *
     * @param  \PHPUnit\Framework\TestCase  $testCase
     * @return mixed
     */
    public function __invoke($testCase);
}
