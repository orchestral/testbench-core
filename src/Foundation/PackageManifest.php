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
    protected $testCase;

    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $basePath
     * @param  string  $manifestPath
     * @param  \Orchestra\Testbench\Contracts\TestCase|null  $testCase
     */
    public function __construct(Filesystem $files, $basePath, $manifestPath, $testCase = null)
    {
        parent::__construct($files, $basePath, $manifestPath);

        if ($testCase instanceof TestCase) {
            $this->setTestbench($testCase);
        }
    }

    /**
     * Create a new package manifest instance from base.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Orchestra\Testbench\Contracts\TestCase|null  $testCase
     * @return static
     */
    public static function swap($app, $testCase = null)
    {
        $base = $app->make(IlluminatePackageManifest::class);

        $app->instance(
            IlluminatePackageManifest::class,
            new static(
                $base->files, $base->basePath, $base->manifestPath, $testCase
            )
        );
    }

    /**
     * Set Testbench.
     *
     * @param  \Orchestra\Testbench\Contracts\TestCase  $testCase
     *
     * @return void
     */
    public function setTestbench(TestCase $testCase): void
    {
        $this->testCase = $testCase;
    }


    /**
     * Get all of the package names that should be ignored.
     *
     * @return array
     */
    protected function packagesToIgnore()
    {
        if (! $this->testCase instanceof TestCase) {
            return [];
        }

        return $this->testCase->ignorePackageDiscoveriesFrom();
    }
}
