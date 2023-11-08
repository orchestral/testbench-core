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
        $loadLaravelMigrations = static::cachedConfigurationForWorkbench()->getWorkbenchAttributes()['install'] ?? false;

        if (! ($loadLaravelMigrations && static::usesTestingConcern(WithWorkbench::class))) {
            return;
        }

        if (! static::usesTestingConcern(RefreshDatabase::class)) {
            $this->loadLaravelMigrations();
        } elseif (is_dir($this->app->basePath('migrations'))) {
            after_resolving($this->app, 'migrator', static function ($migrator, $app) {
                $migrator->path($app->basePath('migrations'));
            });
        }
    }
}
