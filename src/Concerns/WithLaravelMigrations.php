<?php

namespace Orchestra\Testbench\Concerns;

use function Orchestra\Testbench\after_resolving;

trait WithLaravelMigrations
{
    use InteractsWithWorkbench;

    /**
     * Bootstrap with laravel migrations.
     *
     * @return void
     */
    protected function setUpWithLaravelMigrations(): void
    {
        /** @var bool $loadLaravelMigrations */
        $loadLaravelMigrations = static::$cachedConfigurationForWorkbench?->getWorkbenchAttributes()['install'] ?? false;

        if (! ($loadLaravelMigrations && static::usesTestingConcern(WithWorkbench::class))) {
            after_resolving($this->app, 'migrator', function ($migrator, $app) {
                if (is_dir($app->basePath('migrations'))) {
                    $migrator->path($app->basePath('migrations'));
                }
            });
        }
    }
}
