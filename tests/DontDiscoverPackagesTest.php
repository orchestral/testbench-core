<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;

class DontDiscoverPackagesTest extends TestCase
{
    public function test_it_can_auto_detect_packages()
    {
        $loadedProviders = \collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertFalse(\in_array('Fideloper\Proxy\TrustedProxyServiceProvider', $loadedProviders));
        $this->assertFalse(\in_array('Fruitcake\Cors\CorsServiceProvider', $loadedProviders));
        $this->assertFalse(\in_array('Carbon\Laravel\ServiceProvider', $loadedProviders));
    }
}
