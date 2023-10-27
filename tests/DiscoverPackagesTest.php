<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DiscoverPackagesTest extends TestCase
{
    protected $enablesPackageDiscoveries = true;

    #[Test]
    public function it_can_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertTrue(\in_array(\Carbon\Laravel\ServiceProvider::class, $loadedProviders));
    }
}
