<?php

namespace Orchestra\Testbench\Concerns;

use Closure;

trait HandlesDatabases
{
    use Database\HandlesConnections;

    /**
     * Setup database requirements.
     *
     * @param  \Closure  $callback
     */
    protected function setUpDatabaseRequirements(Closure $callback): void
    {
        tap($this->app['config'], function ($config) {
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'mysql', 'MYSQL');
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'pgsql', 'POSTGRES');
            $this->usesDatabaseConnectionsEnvironmentVariables($config, 'sqlsrv', 'MSSQL');
        });

        $this->defineDatabaseMigrations();

        if (method_exists($this, 'parseTestMethodAnnotations')) {
            $this->parseTestMethodAnnotations($this->app, 'define-db');
        }

        $callback();

        $this->defineDatabaseSeeders();
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
     * Define database seeders.
     *
     * @return void
     */
    protected function defineDatabaseSeeders()
    {
        // Define database seeders.
    }
}
