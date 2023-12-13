<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\TestCase;

/**
 * @requires PHP >= 8.0
 */
#[WithEnv('TESTING_USING_ATTRIBUTE', '(true)')]
class WithEnvTest extends TestCase
{
    /** @test */
    public function it_can_resolve_defined_env_variables()
    {
        $this->assertSame(true, Env::get('TESTING_USING_ATTRIBUTE'));
    }
}
