<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\TestCase;

class WithWorkbenchTest extends TestCase
{
    use WithWorkbench;

    /** @test */
    public function it_can_be_resolved()
    {
        $this->assertInstanceOf(ConfigContract::class, static::$cachedConfigurationForWorkbench);

        $this->assertSame(static::$cachedConfigurationForWorkbench, static::cachedConfigurationForWorkbench());

        $this->assertSame([
            'env' => ["APP_NAME='Testbench'"],
            'bootstrappers' => [],
            'providers' => ['Workbench\App\Providers\TestbenchServiceProvider'],
            'dont-discover' => [],
        ], static::$cachedConfigurationForWorkbench->getExtraAttributes());
    }
}
