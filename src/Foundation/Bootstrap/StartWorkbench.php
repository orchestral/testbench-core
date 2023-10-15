<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Contracts\Config;
use Orchestra\Testbench\Workbench\WorkbenchServiceProvider as FallbackServicePorvider;
use Orchestra\Workbench\WorkbenchServiceProvider;

/**
 * @internal
 */
class StartWorkbench
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
        $app->register(
            class_exists(WorkbenchServiceProvider::class)
                ? WorkbenchServiceProvider::class
                : FallbackServicePorvider::class
        );
    }
}
