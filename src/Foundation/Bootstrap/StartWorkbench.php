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
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     */
    public function __construct(
        public Config $config,
    ) {
        //
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->singleton(Config::class, fn () => $this->config);

        $this->loadWorkbenchProviders($app);
    }

    /**
     * Load Workbench providers.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function loadWorkbenchProviders(Application $app): void
    {
        if (class_exists(WorkbenchServiceProvider::class)) {
            $app->register(WorkbenchServiceProvider::class);
        }
    }
}
