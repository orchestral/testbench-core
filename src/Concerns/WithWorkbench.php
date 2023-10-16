<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Foundation\Bootstrap\DiscoverRoutes;
use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;
use Orchestra\Testbench\Foundation\Bootstrap\StartWorkbench;

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

        $app->booted(static function ($app) use ($config) {
            (new DiscoverRoutes($config))->bootstrap($app);
        });

        (new LoadMigrationsFromArray(
            $config['migrations'] ?? [], $config['seeders'] ?? false,
        ))->bootstrap($app);
    }
}
