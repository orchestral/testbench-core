<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as IlluminatePackageManifest;
use Orchestra\Testbench\Contracts\TestCase;

class PackageManifest extends IlluminatePackageManifest
{
    /**
     * Testbench Class.
     *
     * @var \Orchestra\Testbench\Contracts\TestCase|null
     */
    protected $testbench;

    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $basePath
     * @param  string  $manifestPath
     * @param  \Orchestra\Testbench\Contracts\TestCase|null  $testbench
     */
    public function __construct(Filesystem $files, $basePath, $manifestPath, $testbench = null)
    {
        parent::__construct($files, $basePath, $manifestPath);

        if ($testbench instanceof TestCase) {
            $this->setTestbench($testbench);
        }
    }

    /**
     * Create a new package manifest instance from base.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Orchestra\Testbench\Contracts\TestCase|null  $testbench
     * @return static
     */
    public static function swap($app, $testbench = null)
    {
        $base = $app->make(IlluminatePackageManifest::class);

        $app->instance(
            IlluminatePackageManifest::class,
            new static(
                $base->files, $base->basePath, $base->manifestPath, $testbench
            )
        );
    }

    /**
     * Set Testbench.
     *
     * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
     *
     * @return void
     */
    public function setTestbench(TestCase $testbench): void
    {
        $this->testbench = $testbench;
    }


    /**
     * Get all of the package names that should be ignored.
     *
     * @return array
     */
    protected function packagesToIgnore()
    {
        if (! $this->testbench instanceof TestCase) {
            return [];
        }

        return $this->testbench->ignorePackageDiscoveriesFrom();
    }
}
