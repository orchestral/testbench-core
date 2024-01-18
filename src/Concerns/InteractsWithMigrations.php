<?php

namespace Orchestra\Testbench\Concerns;

use InvalidArgumentException;
use Orchestra\Testbench\Database\MigrateProcessor;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;

use function Orchestra\Testbench\laravel_migration_path;

/**
 * @internal
 */
trait InteractsWithMigrations
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @api
     *
     * @param  string|array<string, mixed>  $paths
     * @return void
     */
    protected function loadMigrationsFrom(string|array $paths): void
    {
        $this->loadMigrationsWithoutRollbackFrom($paths);

        $this->beforeApplicationDestroyed(function () use ($paths) {
            (new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths)))->rollback();
        });
    }

    /**
     * Define hooks to migrate the database before each test without rollback after.
     *
     * @api
     *
     * @param  string|array<string, mixed>  $paths
     * @return void
     */
    protected function loadMigrationsWithoutRollbackFrom(string|array $paths): void
    {
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $migrator = new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Resolve Package Migrations Artisan command options.
     *
     * @internal
     *
     * @param  string|array<string, mixed>  $paths
     * @return array
     */
    protected function resolvePackageMigrationsOptions(string|array $paths = []): array
    {
        $options = \is_array($paths) ? $paths : ['--path' => $paths];

        if (isset($options['--realpath']) && ! \is_bool($options['--realpath'])) {
            throw new InvalidArgumentException('Expect --realpath to be a boolean.');
        }

        $options['--realpath'] = true;

        return $options;
    }

    /**
     * Migrate Laravel's default migrations.
     *
     * @api
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function loadLaravelMigrations(string|array $database = []): void
    {
        $this->loadLaravelMigrationsWithoutRollback($database);

        $this->beforeApplicationDestroyed(function () use ($database) {
            $options = $this->resolveLaravelMigrationsOptions($database);
            $options['--path'] = laravel_migration_path();
            $options['--realpath'] = true;

            (new MigrateProcessor($this, $options))->rollback();
        });
    }

    /**
     * Migrate Laravel's default migrations without rollback.
     *
     * @api
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function loadLaravelMigrationsWithoutRollback(string|array $database = []): void
    {
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $options = $this->resolveLaravelMigrationsOptions($database);
        $options['--path'] = laravel_migration_path();
        $options['--realpath'] = true;

        (new MigrateProcessor($this, $options))->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Migrate all Laravel's migrations.
     *
     * @api
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function runLaravelMigrations(string|array $database = []): void
    {
        $this->runLaravelMigrationsWithoutRollback($database);

        $this->beforeApplicationDestroyed(function () use ($database) {
            (new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database)))->rollback();
        });
    }

    /**
     * Migrate all Laravel's migrations without rollback.
     *
     * @api
     *
     * @param  string|array<string, mixed>  $database
     * @return void
     */
    protected function runLaravelMigrationsWithoutRollback(string|array $database = []): void
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
     * @internal
     *
     * @param  string|array<string, mixed>  $database
     * @return array
     */
    protected function resolveLaravelMigrationsOptions(string|array $database = []): array
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        return $options;
    }
}
