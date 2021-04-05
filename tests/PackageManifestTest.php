<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\PackageManifest;
use Orchestra\Testbench\TestCase;

class PackageManifestTest extends TestCase
{
    /** @test */
    public function it_can_build_manifest()
    {
        if (! \defined('TESTBENCH_WORKING_PATH')) {
            \define('TESTBENCH_WORKING_PATH', \realpath(__DIR__.'/../'));
        }

        $packageManifest = new PackageManifest(
            $this->app['files'], $this->app->basePath(), $manifestPath = realpath(__DIR__.'/tmp').'/manifest.php', $this
        );

        $packageManifest->build();

        $packages = Collection::make(require $manifestPath);

        $this->assertSame([
            'fideloper/proxy',
            'fruitcake/laravel-cors',
            'laravel/laravel',
            'laravel/tinker',
            'nesbot/carbon',
            'orchestra/canvas',
            'orchestra/canvas-core',
            'spatie/laravel-ray',
        ], $packages->keys()->all());

        $this->app['files']->delete($manifestPath);
    }
}
