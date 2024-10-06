<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;
use Orchestra\Testbench\Foundation\Bootstrap\LoadMigrationsFromArray;
use Orchestra\Testbench\Workbench\Workbench;

trait WithWorkbench
{
    use InteractsWithPHPUnit;
    use InteractsWithWorkbench;

    /**
     * Bootstrap with Workbench.
     *
     * @internal
     *
     * @return void
     */
    protected function setUpWithWorkbench(): void
    {
        /** @var \Illuminate\Contracts\Foundation\Application $app */
        $app = $this->app;

        /** @var \Orchestra\Testbench\Contracts\Config $config */
        $config = static::cachedConfigurationForWorkbench();

        Workbench::start($app, $config);

        $seeders = $config['seeders'] ?? false;

        if (static::usesTestingConcern(CanConfigureMigrationCommands::class) && $this->shouldSeed() === false) {
            $seeders = false;
        }

        (new LoadMigrationsFromArray(
            $config['migrations'] ?? [], $seeders,
        ))->bootstrap($app);
    }

    /**
     * Bootstrap discover routes.
     *
     * @internal
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function bootDiscoverRoutesForWorkbench($app): void
    {
        /** @var \Orchestra\Testbench\Contracts\Config $config */
        $config = static::cachedConfigurationForWorkbench();

        Workbench::discoverRoutes($app, $config);
    }
}
