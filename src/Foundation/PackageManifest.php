<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as IlluminatePackageManifest;
use Illuminate\Support\Collection;

class PackageManifest extends IlluminatePackageManifest
{
    /**
     * Testbench Class.
     *
     * @var \Orchestra\Testbench\Contracts\TestCase|null
     */
    protected $testbench;

    /**
     * List of required packages.
     *
     * @var array
     */
    protected $requiredPackages = [
        'spatie/laravel-ray',
    ];

    /**
     * Create a new package manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $basePath
     * @param  string  $manifestPath
     * @param  object|null  $testbench
     */
    public function __construct(Filesystem $files, $basePath, $manifestPath, $testbench = null)
    {
        parent::__construct($files, $basePath, $manifestPath);

        $this->setTestbench($testbench);
    }

    /**
     * Create a new package manifest instance from base.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  object|null  $testbench
     *
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
     * Set Testbench instance.
     *
     * @param  object|null  $testbench
     *
     * @return void
     */
    public function setTestbench($testbench): void
    {
        $this->testbench = \is_object($testbench) ? $testbench : null;
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $packages = [];

        if ($this->files->exists($path = $this->vendorPath.'/composer/installed.json')) {
            $installed = \json_decode($this->files->get($path), true);

            $packages = $installed['packages'] ?? $installed;
        }

        $ignore = $this->packagesToIgnore();

        $this->write(
            Collection::make($packages)->mapWithKeys(function ($package) {
                return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
            })
            ->merge($this->providersFromRoot())
            ->each(static function ($configuration) use (&$ignore) {
                $ignore = \array_merge($ignore, $configuration['dont-discover'] ?? []);
            })->reject(static function ($configuration, $package) use ($ignore) {
                return \in_array($package, $ignore);
            })->filter()->all()
        );
    }

    /**
     * Get the current package manifest.
     *
     * @return array
     */
    protected function getManifest()
    {
        $ignore = ! \is_null($this->testbench) && \method_exists($this->testbench, 'ignorePackageDiscoveriesFrom')
                ? ($this->testbench->ignorePackageDiscoveriesFrom() ?? [])
                : [];

        $ignoreAll = \in_array('*', $ignore);

        return Collection::make(parent::getManifest())
            ->merge($this->providersFromRoot())
            ->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
                return ($ignoreAll && ! \in_array($package, $this->requiredPackages))
                    || \in_array($package, $ignore);
            })->map(static function ($configuration, $key) {
                foreach ($configuration['providers'] ?? [] as $provider) {
                    if (! \class_exists($provider)) {
                        return null;
                    }
                }

                return $configuration;
            })->filter()->all();
    }

    /**
     * Get all of the package names that should be ignored.
     *
     * @return array
     */
    protected function packagesToIgnore()
    {
        return [];
    }

    /**
     * Get all of the package names from root.
     *
     * @return array
     */
    protected function providersFromRoot()
    {
        if (! \defined('TESTBENCH_WORKING_PATH') || ! \is_file(TESTBENCH_WORKING_PATH.'/composer.json')) {
            return [];
        }

        $package = \json_decode(\file_get_contents(
            TESTBENCH_WORKING_PATH.'/composer.json'
        ), true);

        return [
            $this->format($package['name']) => $package['extra']['laravel'] ?? [],
        ];
    }
}
