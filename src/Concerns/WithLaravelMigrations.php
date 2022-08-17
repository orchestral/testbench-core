<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\MigrateProcessor;

trait WithLaravelMigrations
{
    /**
     * Migrate Laravel's default migrations.
     *
     * @param  string|array<string, mixed>  $database
     * @param  bool                         $rollback
     * @return void
     */
    protected function loadLaravelMigrations($database = [], bool $rollback = true): void
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        $options['--path'] = 'migrations';

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        if ($rollback) {
            $this->beforeApplicationDestroyed(fn () => $migrator->rollback());
        }
    }

    /**
     * Migrate all Laravel's migrations.
     *
     * @param  string|array<string, mixed>  $database
     * @param  bool                         $rollback
     * @return void
     */
    protected function runLaravelMigrations($database = [], bool $rollback = true): void
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        if ($rollback) {
            $this->beforeApplicationDestroyed(fn () => $migrator->rollback());
        }
    }
}
