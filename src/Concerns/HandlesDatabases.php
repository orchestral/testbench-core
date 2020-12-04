<?php

namespace Orchestra\Testbench\Concerns;

use Closure;

trait HandlesDatabases
{
    /**
     * Setup database requirements.
     *
     * @param  \Closure  $callback
     */
    protected function setUpDatabaseRequirements(Closure $callback): void
    {
        $this->defineDatabaseMigrations();

        $this->parseTestMethodAnnotations($this->app, 'define-db');

        $callback();
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
     * Parse test method annotations.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    abstract protected function parseTestMethodAnnotations($app, string $name): void;
}
