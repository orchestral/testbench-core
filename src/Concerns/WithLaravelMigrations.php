<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\MigrateProcessor;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;

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
        $this->loadLaravelMigrationsWithoutRollback($database);

        $this->beforeApplicationDestroyed(function () use ($database) {
            $options = $this->resolveLaravelMigrationsOptions($database);
            $options['--path'] = 'migrations';

            (new MigrateProcessor($this, $options))->rollback();
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
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $options = $this->resolveLaravelMigrationsOptions($database);
        $options['--path'] = 'migrations';

        (new MigrateProcessor($this, $options))->up();

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
        $this->runLaravelMigrationsWithoutRollback($database);

        $this->beforeApplicationDestroyed(function () use ($database) {
            (new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database)))->rollback();
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
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        (new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database)))->up();

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

        return $options;
    }
}
