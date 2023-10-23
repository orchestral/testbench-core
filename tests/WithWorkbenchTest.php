<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Workbench\Workbench;

class WithWorkbenchTest extends TestCase
{
    use WithWorkbench;

    /**
     * @test
     */
    public function it_can_be_resolved()
    {
        $cachedConfig = Workbench::configuration();

        $this->assertInstanceOf(ConfigContract::class, $cachedConfig);

        $this->assertSame($cachedConfig, static::cachedConfigurationForWorkbench());

        $this->assertSame([
            'env' => ["APP_NAME='Testbench'"],
            'bootstrappers' => [],
            'providers' => ['Workbench\App\Providers\WorkbenchServiceProvider'],
            'dont-discover' => [],
        ], $cachedConfig->getExtraAttributes());
    }

    /**
     * @test
     */
    public function it_can_be_manually_resolved()
    {
        $cachedConfig = static::cachedConfigurationForWorkbench();

        Workbench::flush();

        $config = static::cachedConfigurationForWorkbench();

        $this->assertInstanceOf(ConfigContract::class, $config);

        $this->assertSame($cachedConfig->toArray(), $config->toArray());
    }
}
