<?php

namespace Orchestra\Testbench\Tests;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\PackageManifest;
use Orchestra\Testbench\TestCase;

class PackageManifestTest extends TestCase
{
    /**
     * @test
     * @group core
     */
    public function it_can_build_manifest()
    {
        if (! \defined('TESTBENCH_WORKING_PATH')) {
            \define('TESTBENCH_WORKING_PATH', realpath(__DIR__.'/../'));
        }

        $manifestPath = realpath(__DIR__.'/tmp').'/manifest.php';

        $packageManifest = new PackageManifest(
            $this->app['files'], $this->app->basePath(), $manifestPath, $this
        );

        $packageManifest->build();

        $packages = Collection::make(require $manifestPath);

        $installedPackages = [
            'fruitcake/laravel-cors',
            'laravel/laravel',
            'laravel/tinker',
            'nesbot/carbon',
            'orchestra/canvas',
            'orchestra/canvas-core',
            'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }
}
