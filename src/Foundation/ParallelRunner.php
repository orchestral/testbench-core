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
        $config = Config::loadFromYaml($_SERVER['TESTBENCH_WORKING_PATH']);

        $workingPath =  $config['laravel'] ??  $_SERVER['APP_BASE_PATH'];

        $hasEnvironmentFile = file_exists("{$workingPath}/.env");

        return Application::create(
            basePath: $workingPath,
            options: ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
        );
    }
}
