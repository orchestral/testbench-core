<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $loadLaravelMigrations = optional(static::$cachedConfigurationForWorkbench)->getWorkbenchAttributes()['install'] ?? false;

        if (! ($loadLaravelMigrations && static::usesTestingConcern(WithWorkbench::class))) {
            if (! is_dir($this->app->basePath('migrations'))) {
                return;
            }

            if (! static::usesTestingConcern(RefreshDatabase::class)) {
                $this->loadLaravelMigrations();
            } else {
                after_resolving($this->app, 'migrator', function ($migrator, $app) {
                    $migrator->path($app->basePath('migrations'));
                });
            }
        }
    }
}
