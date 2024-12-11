<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use function Orchestra\Testbench\package_path;

/**
 * @api
 */
abstract class Action
{
    /**
     * Working path for the action.
     *
     * @var string|null
     */
    public $workingPath;

    /**
     * Normalise file location.
     *
     * @param  string  $path
     * @return string
     */
    protected function pathLocation(string $path): string
    {
        $packagePath = package_path();

        if (! \is_null($this->workingPath)) {
            $path = str_replace(rtrim($this->workingPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR, '', $path);

            $prefix = match ($this->workingPath) {
                app()->basePath() => '@laravel',
                $packagePath => '.',
                default => '@'
            };

            return implode('/', [$prefix, ltrim($path, '/')]);
        }

        if (str_starts_with($path, $packagePath)) {
            return \sprintf('./%s', ltrim(str_replace($packagePath, '', $path), '/'));
        }

        return $path;
    }
}
