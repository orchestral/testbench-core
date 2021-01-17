<?php

namespace Orchestra\Testbench\Foundation;

use Orchestra\Testbench\Concerns\CreatesApplication;

class ParallelRunner extends \Illuminate\Testing\ParallelRunner
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function createApplication()
    {
        $applicationResolver = static::$applicationResolver ?: function () {
            $applicationCreator = new class {
                use CreatesApplication;
            };

            return $applicationCreator->createApplication();
        };

        return call_user_func($applicationResolver);
    }
}
