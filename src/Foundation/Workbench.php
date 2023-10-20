<?php

namespace Orchestra\Testbench\Foundation;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Env;

use function Orchestra\Testbench\workbench_path;

class Workbench
{
    /**
     * The cached test case configuration.
     *
     * @var \Orchestra\Testbench\Contracts\Config|null
     */
    protected static $cachedConfiguration;

    /**
     * The cached core workbench bindings.
     *
     * @var array{kernel: array{console?: string|null, http?: string|null}, handler: array{exception?: string|null}}
     */
    public static $cachedCoreBindings = [
        'kernel' => [],
        'handler' => [],
    ];

    /**
     * Resolve the configuration.
     *
     * @return \Orchestra\Testbench\Contracts\Config
     */
    public static function configuration(): ConfigContract
    {
        if (\is_null(static::$cachedConfiguration)) {
            $workingPath = getcwd();

            if (\defined('TESTBENCH_WORKING_PATH')) {
                $workingPath = TESTBENCH_WORKING_PATH;
            } elseif (! \is_null(Env::get('TESTBENCH_WORKING_PATH'))) {
                $workingPath = Env::get('TESTBENCH_WORKING_PATH');
            }

            static::$cachedConfiguration = Config::cacheFromYaml($workingPath);
        }

        return static::$cachedConfiguration;
    }

    /**
     * Get application Console Kernel implementation.
     *
     * @return string|null
     */
    public static function applicationConsoleKernel(): ?string
    {
        if (! isset(static::$cachedCoreBindings['kernel']['console'])) {
            static::$cachedCoreBindings['kernel']['console'] = file_exists(workbench_path('/app/Console/Kernel.php'))
                ? 'Workbench\App\Console\Kernel'
                : null;
        }

        return static::$cachedCoreBindings['kernel']['console'];
    }

    /**
     * Get application HTTP Kernel implementation using Workbench.
     *
     * @return string|null
     */
    public static function applicationHttpKernel(): ?string
    {
        if (! isset(static::$cachedCoreBindings['kernel']['http'])) {
            static::$cachedCoreBindings['kernel']['http'] = file_exists(workbench_path('/app/Http/Kernel.php'))
                ? 'Workbench\App\Http\Kernel'
                : null;
        }

        return static::$cachedCoreBindings['kernel']['http'];
    }

    /**
     * Get application HTTP exception handler using Workbench.
     *
     * @return string|null
     */
    public static function applicationExceptionHandler(): ?string
    {
        if (! isset(static::$cachedCoreBindings['handler']['exception'])) {
            static::$cachedCoreBindings['handler']['exception'] = file_exists(workbench_path('/app/Exceptions/Exceptions.php'))
                ? 'Workbench\App\Exceptions\Handler'
                : null;
        }

        return static::$cachedCoreBindings['handler']['exception'];
    }

    /**
     * Flush the cached configuration.
     *
     * @return void
     */
    public static function flush(): void
    {
        static::$cachedConfiguration = null;

        static::$cachedCoreBindings = [
            'kernel' => [],
            'handler' => [],
        ];
    }
}
