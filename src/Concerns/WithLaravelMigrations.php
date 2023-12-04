<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\laravel_migration_path;

/**
 * @api
 */
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

        if (! ($loadLaravelMigrations && is_dir(laravel_migration_path()))) {
            return;
        }

        if (! static::usesTestingConcern(LazilyRefreshDatabase::class) && ! static::usesTestingConcern(RefreshDatabase::class)) {
            $this->loadLaravelMigrations();
        } else {
            after_resolving($this->app, 'migrator', static function ($migrator, $app) {
                /** @var \Illuminate\Database\Migrations\Migrator $migrator */
                $migrator->path(laravel_migration_path());
            });
        }
    }
}
