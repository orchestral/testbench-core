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
        $workingPath = $_ENV['APP_BASE_PATH'] ?? null;
        $testbenchWorkingPath = defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : null;

        $config = ! is_null($testbenchWorkingPath)
            ? Config::loadFromYaml($testbenchWorkingPath)
            : new Config();

        $hasEnvironmentFile = ! is_null($workingPath) && file_exists("{$workingPath}/.env");

        return Application::create(
            basePath: $config['laravel'] ?? null,
            options: ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
        );
    }
}
