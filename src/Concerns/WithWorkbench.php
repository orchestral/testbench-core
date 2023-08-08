<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Bootstrap\StartWorkbench;

trait WithWorkbench
{
    /**
     * The cached test case configuration.
     *
     * @var \Orchestra\Testbench\Contracts\Config|null
     */
    protected static $cachedTestCaseConfiguration;

    /**
     * Bootstrap with Workbench.
     *
     * @return void
     */
    protected function setUpWithWorkbench(): void
    {
        /** @var \Illuminate\Contracts\Foundation\Application $app */
        $app = $this->app;

        /** @var \Orchestra\Testbench\Contracts\Config $config */
        $config = static::$cachedTestCaseConfiguration ?? new Config();

        (new StartWorkbench($config))->bootstrap($app);

        (new LoadMigrationsFromArray(
            $config['migrations'] ?? [], $config['seeders'] ?? false,
        ))->bootstrap($app);
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     */
    public static function teardownAfterClassWithWorkbench(): void
    {
        $workingPath = \defined('TESTBENCH_WORKING_PATH')
            ? TESTBENCH_WORKING_PATH
            : getcwd();

        $config = Config::loadFromYaml($workingPath);

        if (! \is_null($config['laravel'])) {
            $_ENV['TESTBENCH_APP_BASE_PATH'] = $config['laravel'];
        }
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     */
    public static function teardownAfterClassWithWorkbench(): void
    {
        static::$cachedTestCaseConfiguration = null;

        unset($_ENV['TESTBENCH_APP_BASE_PATH']);
    }
}
