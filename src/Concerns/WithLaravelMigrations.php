<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\migration_path;

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
        $loadLaravelMigrations = static::cachedConfigurationForWorkbench()->getWorkbenchAttributes()['install'] ?? false;

        if (! ($loadLaravelMigrations && static::usesTestingConcern(WithWorkbench::class))) {
            if (! static::usesTestingConcern(RefreshDatabase::class)) {
                $this->loadLaravelMigrations();
            } else {
                after_resolving($this->app, 'migrator', static function ($migrator, $app) {
                    $migrator->path(...array_filter([
                        is_dir($app->basePath('migrations')) ? $app->basePath('migrations') : null,
                        migration_path('laravel'),
                    ]));
                });
            }
        }
    }
}
