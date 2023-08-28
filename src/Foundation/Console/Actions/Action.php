<?php

namespace Orchestra\Testbench\Foundation\Console\Actions;

use function Orchestra\Testbench\package_path;

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
     * @param  string|null  $workingPath
     * @return string
     */
    protected function pathLocation(string $path, ?string $workingPath = null): string
    {
        $path = str_replace($workingPath ?? package_path('/'), '', $path);

        if (! \is_null($this->workingPath)) {
            $path = str_replace($this->workingPath.'/', '', $path);
        }

        return $path;
    }
}
