<?php

namespace Orchestra\Testbench\Tests\Foundation;

use Closure;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Attributes\UsesVendor;
use Orchestra\Testbench\Foundation\PackageManifest;
use Orchestra\Testbench\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

use function Orchestra\Sidekick\Filesystem\join_paths;

#[UsesVendor]
class PackageManifestTest extends TestCase
{
    #[Test]
    #[Group('core')]
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

        $packages = Collection::make(require $manifestPath);

        $installedPackages = [
            'nesbot/carbon',
            'nunomaduro/termwind',
            // 'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }

    #[Test]
    #[Group('core')]
    public function it_can_build_manifest_without_root_composer_file()
    {
        $packageManifest = new class($this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php'), $this) extends PackageManifest
        {
            protected function providersFromTestbench(): ?array
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
            // 'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }

    #[Test]
    #[Group('core')]
    public function it_can_build_manifest_without_any_discovery()
    {
        $packageManifest = new class($this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php'), $this) extends PackageManifest
        {
            protected function providersFromTestbench(): ?array
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
            // 'spatie/laravel-ray',
        ];

        foreach ($installedPackages as $installedPackage) {
            $this->assertTrue(\in_array($installedPackage, $packages->keys()->all()), "Unable to discover {$installedPackage}");
        }

        $this->app['files']->delete($manifestPath);
    }

    #[Test]
    #[Group('core')]
    public function it_uses_a_package_manifest_per_parallel_testing_worker()
    {
        $this->withParallelTestingEnvironment('7', function () {
            $packageManifest = new PackageManifest(
                $this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php'), $this
            );

            $this->assertSame(
                $this->normalizePath(join_paths(realpath(__DIR__), 'tmp', 'test_7_packages.php')),
                $this->normalizePath($packageManifest->getManifestPath())
            );
        });
    }

    #[Test]
    #[Group('core')]
    public function it_uses_a_package_manifest_per_laravel_parallel_testing_worker()
    {
        $this->withParallelTestingEnvironment('7', function () {
            $packageManifest = new PackageManifest(
                $this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php'), $this
            );

            $this->assertSame(
                $this->normalizePath(join_paths(realpath(__DIR__), 'tmp', 'test_7_packages.php')),
                $this->normalizePath($packageManifest->getManifestPath())
            );
        }, laravelParallelTesting: true);
    }

    #[Test]
    #[Group('core')]
    public function it_keeps_the_default_package_manifest_when_not_testing_in_parallel()
    {
        $this->withParallelTestingEnvironment(null, function () {
            $packageManifest = new PackageManifest(
                $this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php'), $this
            );

            $this->assertSame(
                $this->normalizePath(join_paths(realpath(__DIR__), 'tmp', 'manifest.php')),
                $this->normalizePath($packageManifest->getManifestPath())
            );
        });
    }

    #[Test]
    #[Group('core')]
    public function it_keeps_the_given_package_manifest_without_a_testbench_test_case()
    {
        $this->withParallelTestingEnvironment('7', function () {
            $packageManifest = new PackageManifest(
                $this->app['files'], $this->app->basePath(), join_paths(realpath(__DIR__), 'tmp', 'manifest.php')
            );

            $this->assertSame(
                $this->normalizePath(join_paths(realpath(__DIR__), 'tmp', 'manifest.php')),
                $this->normalizePath($packageManifest->getManifestPath())
            );
        });
    }

    /**
     * Compare paths regardless of the directory separator they were joined with.
     */
    private function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Run the callback as a parallel testing worker (with a token), or as a serial run (without one).
     *
     * Pest and ParaTest set only the token for a package's workers; Laravel's own runner also sets
     * `LARAVEL_PARALLEL_TESTING`.
     *
     * @param  \Closure():void  $callback
     */
    private function withParallelTestingEnvironment(?string $token, Closure $callback, bool $laravelParallelTesting = false): void
    {
        $keys = ['LARAVEL_PARALLEL_TESTING', 'TEST_TOKEN'];
        $previous = [];

        foreach ($keys as $key) {
            $previous[$key] = $_SERVER[$key] ?? null;
            unset($_SERVER[$key]);
        }

        if ($laravelParallelTesting) {
            $_SERVER['LARAVEL_PARALLEL_TESTING'] = '1';
        }

        if (! \is_null($token)) {
            $_SERVER['TEST_TOKEN'] = $token;
        }

        try {
            $callback();
        } finally {
            foreach ($keys as $key) {
                unset($_SERVER[$key]);

                if (! \is_null($previous[$key])) {
                    $_SERVER[$key] = $previous[$key];
                }
            }
        }
    }
}
