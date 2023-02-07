<?php

namespace Orchestra\Testbench\Foundation;

class ParallelRunner extends \Illuminate\Testing\ParallelRunner
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function createApplication()
    {
        $basePath = $_ENV['APP_BASE_PATH'] ?? null;

        return Application::create($basePath);
    }
}
