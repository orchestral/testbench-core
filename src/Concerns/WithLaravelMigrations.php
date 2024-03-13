<?php

namespace Orchestra\Testbench\Concerns;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\laravel_migration_path;

trait WithLaravelMigrations
{
    use InteractsWithWorkbench;

    /**
     * Bootstrap with laravel migrations.
     *
     * @internal
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

        if (! static::usesRefreshDatabaseTestingConcern()) {
            $this->loadLaravelMigrations();
        } else {
            after_resolving($this->app, 'migrator', static function ($migrator, $app) {
                /** @var \Illuminate\Database\Migrations\Migrator $migrator */
                $migrator->path(laravel_migration_path());
            });
        }
    }
}
