<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Foundation\Application as Testbench;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\workbench_path;

trait WithLaravelBootstrapFile
{
    use InteractsWithTestCase;

    /**
     * Get application bootstrap file path (if exists).
     *
     * @internal
     *
     * @param  string  $filename
     * @return string|false
     */
    protected function getApplicationBootstrapFile(string $filename): string|false
    {
        $bootstrapFile = realpath(join_paths($this->getBasePath(), 'bootstrap', $filename));

        if ($this->usesTestbenchDefaultSkeleton()) {
            if (static::usesTestingConcern(WithWorkbench::class) || $this instanceof Testbench) {
                return is_file($workbenchFile = workbench_path(join_paths('bootstrap', $filename))) ? (string) realpath($workbenchFile) : false;
            }

            return false;
        }

        return $bootstrapFile;
    }

    /**
     * Determine if application is using a custom application kernels.
     *
     * @internal
     *
     * @return bool
     */
    protected function hasCustomApplicationKernels(): bool
    {
        return ! $this->usesTestbenchDefaultSkeleton()
            && ((static::$cacheApplicationBootstrapFile ??= $this->getApplicationBootstrapFile('app.php')) !== false);
    }

    /**
     * Determine if application is bootstrapped using Testbench's default skeleton.
     *
     * @return bool
     */
    protected function usesTestbenchDefaultSkeleton(): bool
    {
        return realpath(join_paths($this->getBasePath(), 'bootstrap', '.testbench-default-skeleton')) !== false;
    }

    /**
     * Get base path.
     *
     * @internal
     *
     * @return string
     */
    abstract protected function getBasePath();

    /**
     * Get the default application bootstrap file path (if exists).
     *
     * @internal
     *
     * @param  string  $filename
     * @return string|false
     */
    abstract protected function getDefaultApplicationBootstrapFile(string $filename): string|false;
}
