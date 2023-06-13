<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\PackageManifest;
use Orchestra\Testbench\TestCase;

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

        $manifestPath = realpath(__DIR__.'/tmp').'/manifest.php';

        $packageManifest = new PackageManifest(
            $this->app['files'], $this->app->basePath(), $manifestPath, $this
        );

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
    public function it_can_build_manifest_without_root_composer_file()
    {
        $manifestPath = realpath(__DIR__.'/tmp').'/manifest.php';

        $packageManifest = new class (
            $this->app['files'], $this->app->basePath(), $manifestPath, $this
        ) extends PackageManifest {
            protected function providersFromTestbench()
            {
                return null;
            }
        };

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
        $manifestPath = realpath(__DIR__.'/tmp').'/manifest.php';

        $packageManifest = new class (
            $this->app['files'], $this->app->basePath(), $manifestPath, $this
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
