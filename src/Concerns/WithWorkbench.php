<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Bootstrap\StartWorkbench;

trait WithWorkbench
{
    /**
     * Bootstrap with Workbench.
     *
     * @return void
     */
    protected function setUpWithWorkbench(): void
    {
        /** @var \Illuminate\Contracts\Foundation\Application $app */
        $app = $this->app;

        $workingPath = defined('TESTBENCH_WORKING_PATH')
            ? TESTBENCH_WORKING_PATH
            : getcwd();

        (new StartWorkbench($config = Config::loadFromYaml($workingPath)))->bootstrap($app);

        (new LoadMigrationsFromArray(
            $config['migrations'] ?? [], $config['seeders'] ?? false,
        ))->bootstrap($app);
    }
}
