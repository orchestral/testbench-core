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
            'nunomaduro/termwind',
            'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }

    /**
     * @test
     *
     * @group core
     */
    public function it_can_build_manifest_without_root_composer_file()
    {
        $packageManifest = new class(
            $this->app['files'],
            $this->app->basePath(),
            join_paths(realpath(__DIR__), 'tmp', 'manifest.php'),
            $this
        ) extends PackageManifest {
            protected function providersFromTestbench()
            {
                return null;
            }
        };

        $manifestPath = $packageManifest->getManifestPath();

        $packageManifest->build();

        $packages = Collection::make(require $manifestPath);

        $installedPackages = [
            'nesbot/carbon',
            'nunomaduro/termwind',
            'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }

    /**
     * @test
     *
     * @group core
     */
    public function it_can_build_manifest_without_any_discovery()
    {
        $packageManifest = new class(
            $this->app['files'],
            $this->app->basePath(),
            join_paths(realpath(__DIR__), 'tmp', 'manifest.php'),
            $this
        ) extends PackageManifest {
            protected function providersFromTestbench()
            {
                return [
                    'name' => 'testbench/example',
                    'extra' => [
                        'laravel' => [
                            'dont-discover' => '*',
                        ],
                    ],
                ];
            }
        };

        $manifestPath = $packageManifest->getManifestPath();

        $packageManifest->build();

        $packages = Collection::make(require $manifestPath);

        $installedPackages = [
            'nesbot/carbon',
            'nunomaduro/termwind',
            'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }
}
