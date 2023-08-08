<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Arr;
use Orchestra\Testbench\Foundation\Config;

trait InteractsWithWorkbench
{
    /**
     * The cached test case configuration.
     *
     * @var \Orchestra\Testbench\Contracts\Config|null
     */
    protected static $cachedConfigurationForWorkbench;

    /**
     * Get Application's base path.
     *
     * @return string|null
     */
    public static function applicationBasePathUsingWorkbench()
    {
        return $_ENV['APP_BASE_PATH'] ?? $_ENV['TESTBENCH_APP_BASE_PATH'] ?? null;
    }

    /**
     * Ignore package discovery from.
     *
     * @return array<int, string>|null
     */
    public function ignorePackageDiscoveriesFromUsingWorkbench()
    {
        if (property_exists($this, 'enablesPackageDiscoveries') && $this->enablesPackageDiscoveries === true) {
            return optional(static::$cachedConfigurationForWorkbench)->getExtraAttributes()['dont-discover'] ?? [];
        }

        return null;
    }

    /**
     * Get package bootstrapper.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>|null
     */
    protected function getPackageBootstrappersUsingWorkbench($app)
    {
        if (empty($bootstrappers = (optional(static::$cachedConfigurationForWorkbench)->getExtraAttributes()['bootstrappers'] ?? null))) {
            return null;
        }

        return Arr::wrap($bootstrappers);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>|null
     */
    protected function getPackageProvidersUsingWorkbench($app)
    {
        if (empty($providers = (optional(static::$cachedConfigurationForWorkbench)->getExtraAttributes()['providers'] ?? null))) {
            return null;
        }

        return Arr::wrap($providers);
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     */
    public static function setupBeforeClassUsingWorkbench(): void
    {
        $workingPath = \defined('TESTBENCH_WORKING_PATH')
            ? TESTBENCH_WORKING_PATH
            : getcwd();

        $config = Config::loadFromYaml($workingPath);

        if (! \is_null($config['laravel'])) {
            $_ENV['TESTBENCH_APP_BASE_PATH'] = $config['laravel'];
        }

        static::$cachedConfigurationForWorkbench = $config;
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     */
    public static function teardownAfterClassUsingWorkbench(): void
    {
        static::$cachedConfigurationForWorkbench = null;

        unset($_ENV['TESTBENCH_APP_BASE_PATH']);
    }
}
