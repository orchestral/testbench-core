<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;

class DiscoverPackagesTest extends TestCase
{
    use WithWorkbench;

    protected $enablesPackageDiscoveries = true;

    /** @test */
    public function it_can_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertContains('Carbon\Laravel\ServiceProvider', $loadedProviders);
        $this->assertContains('Workbench\App\Providers\AppServiceProvider', $loadedProviders);
    }
}
