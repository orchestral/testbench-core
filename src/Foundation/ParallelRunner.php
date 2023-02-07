<?php

namespace Orchestra\Testbench\Foundation;

use Orchestra\Testbench\Foundation\Application;

class ParallelRunner extends \Illuminate\Testing\ParallelRunner
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function createApplication()
    {
        return Application::create(
            basePath: $_ENV['APP_BASE_PATH'] ?? null,
        );
    }
}
