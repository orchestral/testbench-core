<?php

namespace Orchestra\Testbench\Foundation;

use function Orchestra\Testbench\workbench_path;

class WorkbenchFinder
{
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
}
