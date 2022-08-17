<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\MigrateProcessor;

trait WithLaravelMigrations
{
    /**
     * Migrate Laravel's default migrations.
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function loadLaravelMigrations($database = []): void
    {
        $migrator = new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        $this->beforeApplicationDestroyed(static function () use ($migrator) {
            $migrator->rollback();
        });
    }

    /**
     * Migrate Laravel's default migrations without rollback.
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function loadLaravelMigrationsWithoutRollback($database = []): void
    {
        $migrator = new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Migrate all Laravel's migrations.
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function runLaravelMigrations($database = []): void
    {
        $migrator = new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        $this->beforeApplicationDestroyed(static function () use ($migrator) {
            $migrator->rollback();
        });
    }

    /**
     * Migrate all Laravel's migrations without rollback.
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function runLaravelMigrationsWithoutRollback($database = []): void
    {
        $migrator = new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Resolve Laravel Migrations Artisan command options.
     *
     * @param  string|array<string, mixed>  $database
     * @return array
     */
    protected function resolveLaravelMigrationsOptions($database = []): array
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        $options['--path'] = 'migrations';

        return $options;
    }
}
