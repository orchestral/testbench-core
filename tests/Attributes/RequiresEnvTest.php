<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Closure;
use Illuminate\Foundation\Application;
use Mockery as m;
use Orchestra\Testbench\Attributes\RequiresEnv;
use PHPUnit\Framework\TestCase;

class RequiresEnvTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_should_run_the_test_when_env_variable_is_missing()
    {
        $attribute = new RequiresEnv('TESTBENCH_MISSING_ENV');

        $callback = $attribute->handle($app = m::mock(Application::class), function ($method, $parameters) use ($app) {
            $this->assertSame('markTestSkipped', $method);
            $this->assertSame(["Missing required environment variable `TESTBENCH_MISSING_ENV`"], $parameters);
        });

        $this->assertNull($callback);
    }
}
