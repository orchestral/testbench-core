<?php

namespace Orchestra\Testbench\Tests;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DontDiscoverPackagesTest extends TestCase
{
    /**
     * Ignore package discovery from.
     *
     * @return array
     */
    public function ignorePackageDiscoveriesFrom()
    {
        return ['spatie/laravel-ray', '*'];
    }

    #[Test]
    public function it_cant_auto_detect_packages()
    {
        $loadedProviders = collect($this->app->getLoadedProviders())->keys()->all();

        $this->assertFalse(\in_array('Spatie\LaravelRay\RayServiceProvider', $loadedProviders));
        $this->assertFalse(\in_array('Carbon\Laravel\ServiceProvider', $loadedProviders));
    }
}
