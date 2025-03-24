<?php

namespace Orchestra\Testbench\Tests;

use PHPUnit\Framework\Attributes\Test;

class DontDiscoverPackagesTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    public function ignorePackageDiscoveriesFrom()
    {
        return ['spatie/laravel-ray', '*'];
    }

    #[Test]
    public function it_cant_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertNotContains('Spatie\LaravelRay\RayServiceProvider', $loadedProviders);
        $this->assertNotContains('Carbon\Laravel\ServiceProvider', $loadedProviders);
        $this->assertNotContains('Workbench\App\Providers\AppServiceProvider', $loadedProviders);
    }
}
