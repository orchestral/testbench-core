<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;

class WithWorkbenchTest extends TestCase
{
    use WithWorkbench;

    /**
     * @test
     *
     * @covers Orchestra\Testbench\Concerns\InteractsWithWorkbench
     */
    public function it_can_be_resolved()
    {
        $cachedConfig = static::$cachedConfigurationForWorkbench;

        $this->assertInstanceOf(ConfigContract::class, $cachedConfig);

        $this->assertSame($cachedConfig, static::cachedConfigurationForWorkbench());

        $this->assertSame([
            'env' => ["APP_NAME='Testbench'"],
            'bootstrappers' => [],
            'providers' => ['Workbench\App\Providers\TestbenchServiceProvider'],
            'dont-discover' => [],
        ], $cachedConfig->getExtraAttributes());
    }

    /**
     * @test
     *
     * @covers Orchestra\Testbench\Concerns\InteractsWithWorkbench
     */
    public function it_can_be_manually_resolved()
    {
        $cachedConfig = static::$cachedConfigurationForWorkbench;

        static::$cachedConfigurationForWorkbench = null;

        $config = static::cachedConfigurationForWorkbench();

        $this->assertInstanceOf(ConfigContract::class, $config);

        $this->assertSame($cachedConfig->toArray(), $config->toArray());
    }
}
