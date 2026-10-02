<?php

namespace Orchestra\Testbench\Tests\Attributes;

use Orchestra\Testbench\Attributes\ResetEnvironmentVariables;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ResetEnvironmentVariablesTest extends TestCase
{
    protected function tearDown(): void
    {
        Env::flushState();

        parent::tearDown();
    }

    #[Test]
    public function it_flushes_env_state_before_all()
    {
        Env::disablePutenv();

        (new ResetEnvironmentVariables)->beforeAll();

        $this->assertTrue($this->envPutenv());
    }

    #[Test]
    public function it_flushes_env_state_after_each()
    {
        Env::disablePutenv();

        (new ResetEnvironmentVariables)->afterEach($this->app);

        $this->assertTrue($this->envPutenv());
    }

    private function envPutenv(): bool
    {
        return (new \ReflectionProperty(Env::class, 'putenv'))->getValue();
    }
}
