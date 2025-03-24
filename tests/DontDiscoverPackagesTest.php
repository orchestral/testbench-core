<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;

class DontDiscoverPackagesTest extends TestCase
{
    use WithWorkbench;

    /**
     * Ignore package discovery from.
     *
     * @return array
     */
    public function ignorePackageDiscoveriesFrom()
    {
        return ['spatie/laravel-ray', '*'];
    }

    /** @test */
    public function it_cant_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertNotContains('Spatie\LaravelRay\RayServiceProvider', $loadedProviders);
        $this->assertNotContains('Carbon\Laravel\ServiceProvider', $loadedProviders);
        $this->assertContains('Workbench\App\Providers\AppServiceProvider', $loadedProviders);
    }
}
