<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\MigrateProcessor;

trait WithLaravelMigrations
{
    /**
     * Migrate Laravel's default migrations.
     *
     * @param  string|array<string, mixed>  $database
     *
     * @return void
     */
    protected function loadLaravelMigrations($database = []): void
    {
        $migrator = $this->loadLaravelMigrationsWithoutRollback($database);

        $this->beforeApplicationDestroyed(static function () use ($migrator) {
            $migrator->rollback();
        });
    }

    /**
     * Migrate Laravel's default migrations without rollback.
     *
     * @param  string|array<string, mixed>  $database
     *
     * @return MigrateProcessor
     */
    protected function loadLaravelMigrationsWithoutRollback($database = []): MigrateProcessor
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        $options['--path'] = 'migrations';

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        return $migrator;
    }

    /**
     * Migrate all Laravel's migrations.
     *
     * @param  string|array<string, mixed>  $database
     *
     * @return void
     */
    protected function runLaravelMigrations($database = []): void
    {
        $migrator = $this->runLaravelMigrationsWithoutRollback($database);

        $this->beforeApplicationDestroyed(static function () use ($migrator) {
            $migrator->rollback();
        });
    }

    /**
     * Migrate all Laravel's migrations without rollback.
     *
     * @param  string|array<string, mixed>  $database
     *
     * @return MigrateProcessor
     */
    protected function runLaravelMigrationsWithoutRollback($database = []): MigrateProcessor
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        return $migrator;
    }
}
