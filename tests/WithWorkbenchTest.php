<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;

class WithWorkbenchTest extends TestCase
{
    use WithWorkbench;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set(['database.default' => 'testing']);
    }

    /** @test */
    public function it_can_be_resolved()
    {
        $this->assertInstanceOf(ConfigContract::class, static::$cachedConfigurationForWorkbench);

        $this->assertSame([
            'env' => ["APP_NAME='Testbench'"],
            'bootstrappers' => [],
            'providers' => ['Workbench\App\Providers\TestbenchServiceProvider'],
            'dont-discover' => [],
        ], static::$cachedConfigurationForWorkbench->getExtraAttributes());
    }
}
