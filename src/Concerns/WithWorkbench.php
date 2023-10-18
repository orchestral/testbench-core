<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Foundation\Bootstrap\DiscoverRoutes;
use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;

trait WithWorkbench
{
    use InteractsWithWorkbench;

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
        $config = static::cachedConfigurationForWorkbench();

        (new StartWorkbench($config))->bootstrap($app);

        (new LoadMigrationsFromArray(
            $config['migrations'] ?? [], $config['seeders'] ?? false,
        ))->bootstrap($app);
    }

    /**
     * Bootstrap discover routes.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function bootDiscoverRoutesForWorkbench($app): void
    {
        /** @var \Orchestra\Testbench\Contracts\Config $config */
        $config = static::cachedConfigurationForWorkbench();

        (new DiscoverRoutes($config))->bootstrap($app);
    }
}
