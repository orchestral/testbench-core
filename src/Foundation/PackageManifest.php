<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest as IlluminatePackageManifest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;

use function Orchestra\Testbench\package_path;

/**
 * @api
 */
class PackageManifest extends IlluminatePackageManifest
{
    /**
     * Testbench Class.
     *
     * @var \Orchestra\Testbench\Contracts\TestCase|object|null
     */
    protected $testbench;

    /**
     * List of required packages.
     *
     * @var array<int, string>
     */
    protected $requiredPackages = [
        'spatie/laravel-ray',
    ];

    /**
     * {@inheritDoc}
     *
     * @param  \Orchestra\Testbench\Contracts\TestCase|object|null  $testbench
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
     * @return void
     */
    public static function swap($app, $testbench = null)
    {
        /** @var \Illuminate\Foundation\PackageManifest $base */
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
     * @return void
     */
    public function setTestbench($testbench): void
    {
        $this->testbench = \is_object($testbench) ? $testbench : null;
    }

    /**
     * Requires packages.
     *
     * @param  string[]  $packages
     * @return $this
     */
    public function requires(...$packages)
    {
        $this->requiredPackages = array_merge($this->requiredPackages, Arr::wrap($packages));

        return $this;
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function getManifest()
    {
        $ignore = ! \is_null($this->testbench) && method_exists($this->testbench, 'ignorePackageDiscoveriesFrom')
                ? ($this->testbench->ignorePackageDiscoveriesFrom() ?? [])
                : [];

        $ignoreAll = \in_array('*', $ignore);

        $requires = $this->requiredPackages;

        return Collection::make(parent::getManifest())
            ->reject(static fn ($configuration, $package) => ($ignoreAll && ! \in_array($package, $requires)) || \in_array($package, $ignore))
            ->map(static function ($configuration, $package) {
                foreach ($configuration['providers'] ?? [] as $provider) {
                    if (! class_exists($provider)) {
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
        $composerFile = package_path('composer.json');

        if (! \defined('TESTBENCH_CORE') || ! is_file($composerFile)) {
            return [];
        }

        $package = transform(file_get_contents($composerFile), static function ($json) use ($composerFile) {
            if (json_validate($json) === false) {
                throw new RuntimeException("Unable to parse [{$composerFile}] file");
            }

            return json_decode($json, true);
        });

        return [
            $this->format($package['name']) => $package['extra']['laravel'] ?? [],
        ];
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function write(array $manifest)
    {
        parent::write(
            Collection::make($manifest)->merge($this->providersFromRoot())->filter()->all()
        );
    }
}
