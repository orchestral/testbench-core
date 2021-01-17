<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;

class DontDiscoverPackagesTest extends TestCase
{
    /** @test */
    public function it_cant_auto_detect_packages()
    {
        $loadedProviders = \collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertFalse(\in_array('Fideloper\Proxy\TrustedProxyServiceProvider', $loadedProviders));
        $this->assertFalse(\in_array('Fruitcake\Cors\CorsServiceProvider', $loadedProviders));
        $this->assertFalse(\in_array('Carbon\Laravel\ServiceProvider', $loadedProviders));
    }
}
