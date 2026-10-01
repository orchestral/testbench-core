<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\Foundation\PackageManifest;
use Orchestra\Testbench\Tests\TestCase;

use function Orchestra\Sidekick\Filesystem\join_paths;

#[UsesVendor]
class PackageManifestTest extends TestCase
{
    /**
     * @test
     *
     * @group core
     */
    public function it_can_build_manifest()
    {
        if (! \defined('TESTBENCH_WORKING_PATH')) {
            \define('TESTBENCH_WORKING_PATH', realpath(__DIR__.'/../../'));
        }

        $packageManifest = new PackageManifest(
            $this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php'), $this
        );

        $manifestPath = $packageManifest->getManifestPath();

        $packageManifest->build();

        clearstatcache(true, $manifestPath);

        $packages = Collection::make(require $manifestPath);

        $installedPackages = [
            'nesbot/carbon',
            'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }
}
