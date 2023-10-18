<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Arr;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Env;

use function Orchestra\Testbench\workbench_path;

trait InteractsWithWorkbench
{
    use InteractsWithPHPUnit;

    /**
     * The cached core workbench bindings.
     *
     * @var array{kernel: array{console: string|null, http: string|null}, handler: array{exception: string|null}}
     */
    protected static $cachedWorkbenchBindings = [
        'kernel' => [
            'console' => null,
            'http' => null,
        ],
        'handler' => [
            'exception' => null,
        ],
    ];

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
    public static function applicationBasePathUsingWorkbench(): ?string
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
    public function ignorePackageDiscoveriesFromUsingWorkbench(): ?array
    {
        if (property_exists($this, 'enablesPackageDiscoveries') && \is_bool($this->enablesPackageDiscoveries)) {
            return $this->enablesPackageDiscoveries === false ? ['*'] : [];
        }

        return static::usesTestingConcern(WithWorkbench::class)
            ? (optional(static::$cachedConfigurationForWorkbench)->getExtraAttributes()['dont-discover'] ?? [])
            : null;
    }

    /**
     * Get package bootstrapper.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>|null
     */
    protected function getPackageBootstrappersUsingWorkbench($app): ?array
    {
        if (empty($bootstrappers = (optional(static::$cachedConfigurationForWorkbench)->getExtraAttributes()['bootstrappers'] ?? null))) {
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
    protected function getPackageProvidersUsingWorkbench($app): ?array
    {
        if (empty($providers = (optional(static::$cachedConfigurationForWorkbench)->getExtraAttributes()['providers'] ?? null))) {
            return null;
        }

        return static::usesTestingConcern(WithWorkbench::class)
            ? Arr::wrap($providers)
            : [];
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string
     */
    protected function applicationConsoleKernelUsingWorkbench($app): string
    {
        if (static::usesTestingConcern(WithWorkbench::class)) {
            if (! isset(static::$cachedWorkbenchBindings['kernel']['console'])) {
                static::$cachedWorkbenchBindings['kernel']['console'] = file_exists(workbench_path('/app/Console/Kernel.php'))
                    ? 'Workbench\App\Console\Kernel'
                    : 'Orchestra\Testbench\Console\Kernel';
            }

            return static::$cachedWorkbenchBindings['kernel']['console'];
        }

        return 'Orchestra\Testbench\Console\Kernel';
    }

    /**
     * Get application HTTP Kernel implementation using Workbench.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string
     */
    protected function applicationHttpKernelUsingWorkbench($app): string
    {
        if (static::usesTestingConcern(WithWorkbench::class)) {
            if (! isset(static::$cachedWorkbenchBindings['kernel']['http'])) {
                static::$cachedWorkbenchBindings['kernel']['http'] = file_exists(workbench_path('/app/Http/Kernel.php'))
                    ? 'Workbench\App\Http\Kernel'
                    : 'Orchestra\Testbench\Http\Kernel';
            }

            return static::$cachedWorkbenchBindings['kernel']['http'];
        }

        return 'Orchestra\Testbench\Http\Kernel';
    }

    /**
     * Get application HTTP exception handler using Workbench.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string
     */
    protected function applicationExceptionHandlerUsingWorkbench($app): string
    {
        if (static::usesTestingConcern(WithWorkbench::class)) {
            if (! isset(static::$cachedWorkbenchBindings['handler']['exception'])) {
                static::$cachedWorkbenchBindings['handler']['exception'] = file_exists(workbench_path('/app/Exceptions/Exceptions.php'))
                    ? 'Workbench\App\Exceptions\Handler'
                    : 'Orchestra\Testbench\Exceptions\Handler';
            }

            return static::$cachedWorkbenchBindings['handler']['exception'];
        }

        return 'Orchestra\Testbench\Exceptions\Handler';
    }

    /**
     * Define or get the cached uses for test case.
     *
     * @return \Orchestra\Testbench\Contracts\Config
     */
    public static function cachedConfigurationForWorkbench()
    {
        if (\is_null(static::$cachedConfigurationForWorkbench)) {
            $workingPath = getcwd();

            if (\defined('TESTBENCH_WORKING_PATH')) {
                $workingPath = TESTBENCH_WORKING_PATH;
            } elseif (! \is_null(Env::get('TESTBENCH_WORKING_PATH'))) {
                $workingPath = Env::get('TESTBENCH_WORKING_PATH');
            }

            static::$cachedConfigurationForWorkbench = Config::cacheFromYaml($workingPath);
        }

        return static::$cachedConfigurationForWorkbench;
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
