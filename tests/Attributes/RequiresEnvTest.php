<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\RequiresEnv;
use Orchestra\Testbench\TestCase;

/**
 * @requires PHP >= 8.0
 */
class RequiresEnvTest extends TestCase
{
    /** @test */
    #[RequiresEnv('TESTBENCH_MISSING_ENV', '')]
    public function it_should_run_the_test_when_env_variable_is_missing()
    {
        $this->fail('Test shouldn\'t be executed');
    }
}
