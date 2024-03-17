<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Orchestra\Testbench\Database\MigrateProcessor;

use function Orchestra\Testbench\laravel_migration_path;
use function Orchestra\Testbench\load_migration_paths;

/**
 * @internal
 */
trait InteractsWithMigrations
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @param  array<int|string, mixed>|string  $paths
     * @return void
     */
    protected function loadMigrationsFrom($paths): void
    {
        if (
            (\is_string($paths) || Arr::isList($paths))
            && static::usesTestingConcern(RefreshDatabase::class)
            && RefreshDatabaseState::$migrated === false
        ) {
            /** @var array<int, string>|string $paths */
            load_migration_paths($this->app, $paths);

            return;
        }

        /** @var array<string, mixed>|string $paths */
        $this->loadMigrationsWithoutRollbackFrom($paths);

        $this->beforeApplicationDestroyed(function () use ($paths) {
            (new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths)))->rollback();
        });
    }

    /**
     * Define hooks to migrate the database before each test without rollback after.
     *
     * @param  array<string, mixed>|string  $paths
     * @return void
     */
    protected function loadMigrationsWithoutRollbackFrom($paths): void
    {
        $migrator = new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Resolve Package Migrations Artisan command options.
     *
     * @param  array<string, mixed>|string  $paths
     * @return array
     */
    protected function resolvePackageMigrationsOptions($paths = []): array
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
     * @param  array<string, mixed>|string  $database
     * @return void
     */
    protected function loadLaravelMigrations($database = []): void
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
     * @param  array<string, mixed>|string  $database
     * @return void
     */
    protected function loadLaravelMigrationsWithoutRollback($database = []): void
    {
        $options = $this->resolveLaravelMigrationsOptions($database);
        $options['--path'] = laravel_migration_path();
        $options['--realpath'] = true;

        (new MigrateProcessor($this, $options))->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Migrate all Laravel's migrations.
     *
     * @param  array<string, mixed>|string  $database
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
     * @param  array<string, mixed>|string  $database
     * @return void
     */
    protected function runLaravelMigrationsWithoutRollback($database = []): void
    {
        (new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database)))->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Resolve Laravel Migrations Artisan command options.
     *
     * @param  array<string, mixed>|string  $database
     * @return array
     */
    protected function resolveLaravelMigrationsOptions($database = []): array
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        return $options;
    }
}
