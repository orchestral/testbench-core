<?php

namespace Orchestra\Testbench\Foundation;

use function Orchestra\Testbench\container;

class ParallelRunner extends \Illuminate\Testing\ParallelRunner
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function createApplication()
    {
        $applicationResolver = static::$applicationResolver ?: static function () {
            return container()->createApplication();
        };

        return $applicationResolver();
    }
}
