<?php

namespace Orchestra\Testbench\Tests;

class DiscoverPackagesTest extends TestCase
{
    protected $enablesPackageDiscoveries = true;

    /** @test */
    public function it_can_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertContains('Carbon\Laravel\ServiceProvider', $loadedProviders);
        $this->assertContains('Workbench\App\Providers\AppServiceProvider', $loadedProviders);
    }
}
