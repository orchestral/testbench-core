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
        $workingPath = $_SERVER['APP_BASE_PATH'] ?? null;

        $config = Config::loadFromYaml($_SERVER['TESTBENCH_WORKING_PATH']);

        $hasEnvironmentFile = ! \is_null($workingPath) && file_exists("{$workingPath}/.env");

        return Application::create(
            basePath: $config['laravel'] ?? null,
            options: ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
        );
    }
}
