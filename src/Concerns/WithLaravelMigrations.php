<?php

namespace Orchestra\Testbench\Concerns;

trait WithLaravelMigrations
{
    /**
     * Bootstrap with laravel migrations.
     *
     * @return void
     */
    protected function setUpWithLaravelMigrations(): void
    {
        $this->loadLaravelMigrations();
    }
}
