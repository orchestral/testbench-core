<?php

namespace Orchestra\Testbench\Concerns;

use Closure;
use Illuminate\Database\Events\DatabaseRefreshed;
use Orchestra\Testbench\Attributes\DefineDatabase;
use Orchestra\Testbench\Attributes\ResetRefreshDatabaseState;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Features\TestingFeature;

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
        $this->app['events']->listen(DatabaseRefreshed::class, function () {
            $this->defineDatabaseMigrationsAfterDatabaseRefreshed();
        });

        if (static::usesTestingConcern(WithLaravelMigrations::class)) {
            /** @phpstan-ignore-next-line */
            $this->setUpWithLaravelMigrations();
        }

        TestingFeature::run(
            $this,
            null,
            null,
            function () {
                $this->parseTestMethodAttributes($this->app, ResetRefreshDatabaseState::class);
                $this->parseTestMethodAttributes($this->app, WithMigration::class);
            }
        );

        $attributeCallbacks = TestingFeature::run(
            $this,
            function () {
                $this->defineDatabaseMigrations();

                $this->beforeApplicationDestroyed(function () {
                    $this->destroyDatabaseMigrations();
                });
            },
            function () {
                $this->parseTestMethodAnnotations($this->app, 'define-db');
            },
            function () {
                return $this->parseTestMethodAttributes($this->app, DefineDatabase::class);
            }
        )->get('attribute');

        $callback();

        $attributeCallbacks->handle();

        $this->defineDatabaseSeeders();
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
