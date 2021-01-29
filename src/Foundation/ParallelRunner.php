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
        $applicationCreator = new class() {
            use CreatesApplication;
        };

        return $applicationCreator->createApplication();
    }
}
