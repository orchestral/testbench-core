<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Contracts\Config;
use Orchestra\Workbench\WorkbenchServiceProvider;

/**
 * @internal
 */
final class StartWorkbench
{
    /**
     * The project configuration.
     *
     * @var \Orchestra\Testbench\Contracts\Config
     */
    public $config;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->instance(Config::class, $this->config);

        if (class_exists(WorkbenchServiceProvider::class)) {
            $app->register(WorkbenchServiceProvider::class);
        }
    }
}
