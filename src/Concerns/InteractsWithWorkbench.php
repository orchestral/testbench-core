<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Arr;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Env;

trait InteractsWithWorkbench
{
    use InteractsWithPHPUnit;

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
        if (! static::usesTestingConcern()) {
            return $_ENV['APP_BASE_PATH'] ?? null;
        }

        return $_ENV['APP_BASE_PATH'] ?? $_ENV['TESTBENCH_APP_BASE_PATH'] ?? null;
    }

    /**
     * Ignore package discovery from.
     *
     * @return array<int, string>|null
     */
    public function ignorePackageDiscoveriesFromUsingWorkbench()
    {
        if (property_exists($this, 'enablesPackageDiscoveries') && \is_bool($this->enablesPackageDiscoveries)) {
            return $this->enablesPackageDiscoveries === false ? ['*'] : [];
        }

        return static::usesTestingConcern(WithWorkbench::class)
            ? (static::$cachedConfigurationForWorkbench?->getExtraAttributes()['dont-discover'] ?? [])
            : null;
    }

    /**
     * Get package bootstrapper.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>|null
     */
    protected function getPackageBootstrappersUsingWorkbench($app)
    {
        if (empty($bootstrappers = (static::$cachedConfigurationForWorkbench?->getExtraAttributes()['bootstrappers'] ?? null))) {
            return null;
        }

        return static::usesTestingConcern(WithWorkbench::class)
            ? Arr::wrap($bootstrappers)
            : [];
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>|null
     */
    protected function getPackageProvidersUsingWorkbench($app)
    {
        if (empty($providers = (static::$cachedConfigurationForWorkbench?->getExtraAttributes()['providers'] ?? null))) {
            return null;
        }

        return static::usesTestingConcern(WithWorkbench::class)
            ? Arr::wrap($providers)
            : [];
    }

    /**
     * Define or get the cached uses for test case.
     *
     * @return \Orchestra\Testbench\Contracts\Config|null
     */
    public static function cachedConfigurationForWorkbench()
    {
        return static::$cachedConfigurationForWorkbench ??= Config::cacheFromYaml(
            match (true) {
                \defined('TESTBENCH_WORKING_PATH') => TESTBENCH_WORKING_PATH,
                ! \is_null(Env::get('TESTBENCH_WORKING_PATH')) => Env::get('TESTBENCH_WORKING_PATH'),
                default => getcwd(),
            }
        );
    }

    /**
     * Prepare the testing environment before the running the test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function setupBeforeClassUsingWorkbench(): void
    {
        /** @var array{laravel: string|null} $config */
        $config = static::cachedConfigurationForWorkbench();

        if (
            ! \is_null($config['laravel'])
            && isset(static::$cachedTestCaseUses[WithWorkbench::class])
        ) {
            $_ENV['TESTBENCH_APP_BASE_PATH'] = $config['laravel'];
        }
    }

    /**
     * Clean up the testing environment before the next test case.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public static function teardownAfterClassUsingWorkbench(): void
    {
        static::$cachedConfigurationForWorkbench = null;

        unset($_ENV['TESTBENCH_APP_BASE_PATH']);
    }
}
