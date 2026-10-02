<?php

namespace Orchestra\Testbench\Attributes;

use Attribute;
use Orchestra\Testbench\Contracts\Attributes\AfterEach as AfterEachContract;
use Orchestra\Testbench\Contracts\Attributes\BeforeAll as BeforeAllContract;
use Orchestra\Testbench\Foundation\Env;

#[Attribute(Attribute::TARGET_CLASS)]
final class ResetEnvironmentVariables implements AfterEachContract, BeforeAllContract
{
    /**
     * Handle the attribute.
     *
     * @codeCoverageIgnore
     */
    public function beforeAll(): void
    {
        Env::flushState();
    }

    /**
     * Handle the attribute.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function afterEach($app): void
    {
        Env::flushState();
    }
}
