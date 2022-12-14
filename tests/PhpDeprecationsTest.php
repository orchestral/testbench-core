<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;

class PhpDeprecationsTest extends TestCase
{
    /** @test */
    public function handle_php81_deprecations()
    {
        $this->expectException('ErrorException');
        $this->expectExceptionMessage('zzz');

        trigger_error('zzz', E_USER_DEPRECATED);
    }
}
