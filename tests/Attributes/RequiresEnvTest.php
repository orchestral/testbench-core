<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Illuminate\Foundation\Application;
use Mockery as m;
use Orchestra\Testbench\Attributes\RequiresEnv;
use Orchestra\Testbench\PHPUnit\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RequiresEnvTest extends TestCase
{
    #[Test]
    public function it_should_run_the_test_when_env_variable_is_missing()
    {
        $attribute = new RequiresEnv('TESTBENCH_MISSING_ENV');

        $callback = $attribute->handle(m::mock(Application::class), function ($method, $parameters) {
            $this->assertSame('markTestSkipped', $method);
            $this->assertSame(['Missing required environment variable `TESTBENCH_MISSING_ENV`'], $parameters);
        });

        $this->assertNull($callback);
    }
}
