<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\MigrateProcessor;

trait WithLoadMigrationsFrom
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @param  string|array  $realpah
     *
     * @return void
     */
    protected function loadMigrationsFrom($realpath): void
    {
        $options = is_array($realpath) ? $realpath : ['--path' => $realpath];

        $migrator = new MigrateProcessor($this->app->make('migrator'), $options);
        $migrator->up();

        $this->beforeApplicationDestroyed(function () use ($migrator) {
            $migrator->rollback();
        });
    }
}
