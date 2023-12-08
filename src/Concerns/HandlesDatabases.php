<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Database\Events\DatabaseRefreshed;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\ResetRefreshDatabaseState;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;
use Orchestra\Testbench\Foundation\Bootstrap\SyncDatabaseEnvironmentVariables;

/**
 * @internal
 */
trait HandlesDatabases
{
    /**
     * Setup database requirements.
     *
     * @param  \Closure():void  $callback
     */
    protected function setUpDatabaseRequirements(Closure $callback): void
    {
        if (\is_null($app = $this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        (new SyncDatabaseEnvironmentVariables())->bootstrap($app);

        $app['events']->listen(DatabaseRefreshed::class, function () {
            $this->defineDatabaseMigrationsAfterDatabaseRefreshed();
        });

        if (static::usesTestingConcern(WithLaravelMigrations::class)) {
            /** @phpstan-ignore-next-line */
            $this->setUpWithLaravelMigrations();
        }

        if (static::usesTestingConcern(HandlesAttributes::class)) {
            $this->parseTestMethodAttributes($app, ResetRefreshDatabaseState::class);
        }

        if (static::usesTestingConcern(HandlesAttributes::class)) {
            $this->parseTestMethodAttributes($app, WithMigration::class);
        }

        $attributeCallbacks = $this->resolveTestbenchTestingFeature(
            testCase: function () {
                $this->defineDatabaseMigrations();
            },
            annotation: function () use ($app) {
                $this->parseTestMethodAnnotations($app, 'define-db');
            },
            attribute: function () use ($app) {
                return $this->parseTestMethodAttributes($app, DefineDatabase::class);
            },
            pest: '@defineEnvironment',
        )->get('attribute');

        $callback();

        if (isset($attributeCallbacks) && $attributeCallbacks->isNotEmpty()) {
            $attributeCallbacks->each(function ($callback) {
                value($callback);
            });
        }

        $this->defineDatabaseSeeders();

        $this->beforeApplicationDestroyed(function () {
            $this->destroyDatabaseMigrations();
        });
    }

    /**
     * Determine if using in-memory SQLite database connection
     *
     * @param  string|null  $connection
     * @return bool
     */
    protected function usesSqliteInMemoryDatabaseConnection(?string $connection = null): bool
    {
        if (\is_null($app = $this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app->make('config');

        /** @var string $connection */
        $connection = ! \is_null($connection) ? $connection : $config->get('database.default');

        /** @var array{driver: string, database: string}|null $database */
        $database = $config->get("database.connections.{$connection}");

        return ! \is_null($database) && $database['driver'] === 'sqlite' && $database['database'] == ':memory:';
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        // Define database migrations.
    }

    /**
     * Define database migrations after database refreshed.
     *
     * @return void
     */
    protected function defineDatabaseMigrationsAfterDatabaseRefreshed()
    {
        // Define database migrations after database refreshed.
    }

    /**
     * Destroy database migrations.
     *
     * @return void
     */
    protected function destroyDatabaseMigrations()
    {
        // Destroy database migrations.
    }

    /**
     * Define database seeders.
     *
     * @return void
     */
    protected function defineDatabaseSeeders()
    {
        // Define database seeders.
    }
}
