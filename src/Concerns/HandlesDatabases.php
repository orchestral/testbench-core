<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Database\Events\DatabaseRefreshed;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\WithMigration;

trait HandlesDatabases
{
    use Database\HandlesConnections;

    /**
     * Setup database requirements.
     *
     * @param  \Closure():void  $callback
     */
    protected function setUpDatabaseRequirements(Closure $callback): void
    {
        tap($this->app['config'], function ($config) {
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'mysql', 'MYSQL');
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'pgsql', 'POSTGRES');
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'sqlsrv', 'MSSQL');
        });

        $this->app['events']->listen(DatabaseRefreshed::class, function () {
            $this->defineDatabaseMigrationsAfterDatabaseRefreshed();
        });

        if (static::usesTestingConcern(WithLaravelMigrations::class)) {
            /** @phpstan-ignore-next-line */
            $this->setUpWithLaravelMigrations();
        }

        if (static::usesTestingConcern(HandlesAttributes::class)) {
            $this->parseTestMethodAttributes($this->app, WithMigration::class);
        }

        $this->defineDatabaseMigrations();

        if (static::usesTestingConcern(HandlesAnnotations::class)) {
            $this->parseTestMethodAnnotations($this->app, 'define-db');
        }

        if (static::usesTestingConcern(HandlesAttributes::class)) {
            $this->parseTestMethodAttributes($this->app, DefineDatabase::class);
        }

        $callback();

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
        $app = $this->app;

        $connection = ! \is_null($connection) ? $connection : $app['config']->get('database.default');

        $database = $app['config']->get("database.connections.{$connection}");

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
