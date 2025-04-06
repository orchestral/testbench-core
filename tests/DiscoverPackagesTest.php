<?php

namespace Orchestra\Testbench\Tests;

use PHPUnit\Framework\Attributes\Test;

class DiscoverPackagesTest extends TestCase
{
    protected $enablesPackageDiscoveries = true;

    #[Test]
    public function it_can_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertContains('Carbon\Laravel\ServiceProvider', $loadedProviders);
        $this->assertNotContains('Workbench\App\Providers\AppServiceProvider', $loadedProviders);
    }
}
