<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Orchestra\Testbench\Attributes\ResetRefreshDatabaseState;
use Orchestra\Testbench\Database\MigrateProcessor;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;

use function Orchestra\Testbench\default_migration_path;
use function Orchestra\Testbench\load_migration_paths;

/**
 * @internal
 */
trait InteractsWithMigrations
{
    /**
     * List of cached migrators instances.
     *
     * @var array<int, \Orchestra\Testbench\Database\MigrateProcessor>
     */
    protected $cachedTestMigratorProcessors = [];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUpInteractsWithMigrations(): void
    {
        if ($this->usesSqliteInMemoryDatabaseConnection()) {
            $this->afterApplicationCreated(static function () {
                static::usesTestingFeature(new ResetRefreshDatabaseState);
            });
        }
    }

    /**
     * Teardown the test environment.
     *
     * @return void
     */
    protected function tearDownInteractsWithMigrations(): void
    {
        if (
            (\count($this->cachedTestMigratorProcessors) > 0 && static::usesRefreshDatabaseTestingConcern())
            || ($this->usesSqliteInMemoryDatabaseConnection() && ! empty(RefreshDatabaseState::$inMemoryConnections))
        ) {
            ResetRefreshDatabaseState::run();
        }

        foreach ($this->cachedTestMigratorProcessors as $migrator) {
            $migrator->rollback();
        }
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @api
     *
     * @param  array<int|string, mixed>|string  $paths
     * @return void
     */
    protected function loadMigrationsFrom(array|string $paths): void
    {
        if (
            (\is_string($paths) || Arr::isList($paths))
            && static::usesRefreshDatabaseTestingConcern()
            && RefreshDatabaseState::$migrated === false
            && RefreshDatabaseState::$lazilyRefreshed === false
        ) {
            if (\is_null($this->app)) {
                throw ApplicationNotAvailableException::make(__METHOD__);
            }

            /** @var array<int, string>|string $paths */
            load_migration_paths($this->app, $paths);

            return;
        }

        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        /** @var array<string, mixed>|string $paths */
        $migrator = new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths));
        $migrator->up();

        array_unshift($this->cachedTestMigratorProcessors, $migrator);

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Define hooks to migrate the database before each test without rollback after.
     *
     * @api
     *
     * @param  array<string, mixed>|string  $paths
     * @return void
     *
     * @deprecated
     */
    #[\Deprecated(message: 'Use `loadMigrationsFrom()` instead', since: '9.0.7')]
    protected function loadMigrationsWithoutRollbackFrom(array|string $paths): void
    {
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $migrator = new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths));
        $migrator->up();

        array_unshift($this->cachedTestMigratorProcessors, $migrator);

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Resolve Package Migrations Artisan command options.
     *
     * @internal
     *
     * @param  array<string, mixed>|string  $paths
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException
     */
    protected function resolvePackageMigrationsOptions(array|string $paths = []): array
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
     * @param  array<string, mixed>|string  $database
     * @return void
     */
    protected function loadLaravelMigrations(array|string $database = []): void
    {
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $options = $this->resolveLaravelMigrationsOptions($database);
        $options['--path'] = default_migration_path();
        $options['--realpath'] = true;

        $migrator = new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($options));
        $migrator->up();

        array_unshift($this->cachedTestMigratorProcessors, $migrator);

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Migrate Laravel's default migrations without rollback.
     *
     * @api
     *
     * @param  array<string, mixed>|string  $database
     * @return void
     *
     * @deprecated
     */
    #[\Deprecated(message: 'Use `loadLaravelMigrations()` instead', since: '9.0.7')]
    protected function loadLaravelMigrationsWithoutRollback(array|string $database = []): void
    {
        $this->loadLaravelMigrations($database);
    }

    /**
     * Migrate all Laravel's migrations.
     *
     * @api
     *
     * @param  array<string, mixed>|string  $database
     * @return void
     */
    protected function runLaravelMigrations(array|string $database = []): void
    {
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $migrator = new MigrateProcessor($this, $this->resolveLaravelMigrationsOptions($database));
        $migrator->up();

        array_unshift($this->cachedTestMigratorProcessors, $migrator);

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Migrate all Laravel's migrations without rollback.
     *
     * @api
     *
     * @param  array<string, mixed>|string  $database
     * @return void
     *
     * @deprecated
     */
    #[\Deprecated(message: 'Use `runLaravelMigrations()` method instead', since: '9.0.7')]
    protected function runLaravelMigrationsWithoutRollback(array|string $database = []): void
    {
        $this->runLaravelMigrations($database);
    }

    /**
     * Resolve Laravel Migrations Artisan command options.
     *
     * @internal
     *
     * @param  array<string, mixed>|string  $database
     * @return array<string, mixed>
     */
    protected function resolveLaravelMigrationsOptions(array|string $database = []): array
    {
        $options = \is_array($database) ? $database : ['--database' => $database];

        return $options;
    }
}
