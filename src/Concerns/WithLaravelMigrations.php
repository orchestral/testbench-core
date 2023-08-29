<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\MigrateProcessor;

trait WithLaravelMigrations
{
    /**
     * Bootstrap with laravel migrations.
     *
     * @return void
     */
    abstract protected function setUpWithLaravelMigrations()
    {
        $this->loadLaravelMigrations();
    }
}
