<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\laravel_migration_path;

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
        if (\is_null($app = $this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        tap($app['config'], function ($config) {
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'mysql', 'MYSQL');
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'pgsql', 'POSTGRES');
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'sqlsrv', 'MSSQL');
        });

        $app['events']->listen(DatabaseRefreshed::class, function () {
            $this->defineDatabaseMigrationsAfterDatabaseRefreshed();
        });

        if (static::usesTestingConcern(WithLaravelMigrations::class)) {
            /** @phpstan-ignore-next-line */
            $this->setUpWithLaravelMigrations();
        }

        if (static::usesTestingConcern(HandlesAttributes::class)) {
            $this->parseTestMethodAttributes($app, WithMigration::class, static function (WithMigration $attribute) use ($app) {
                after_resolving($app, 'migrator', static function ($migrator) use ($attribute) {
                    /** @var \Illuminate\Database\Migrations\Migrator $migrator */
                    Collection::make($attribute->types)
                        ->transform(static function ($type) {
                            return laravel_migration_path($type !== 'laravel' ? $type : null);
                        })->each(static function ($migration) use ($migrator) {
                            $migrator->path($migration);
                        });
                });
            });
        }

        $this->defineDatabaseMigrations();

        if (static::usesTestingConcern(HandlesAnnotations::class)) {
            $this->parseTestMethodAnnotations($app, 'define-db');
        }

        if (static::usesTestingConcern(HandlesAttributes::class)) {
            $this->parseTestMethodAttributes($app, DefineDatabase::class);
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
