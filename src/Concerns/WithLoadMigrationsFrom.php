<?php

namespace Orchestra\Testbench\Concerns;

use Orchestra\Testbench\Database\Migrator;

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

        $migrator = tap(new Migrator($this->app->make('migrator')), function ($schema) use ($options) {
            $schema->up($options);
        });

        $this->beforeApplicationDestroyed(function () use ($migrator, $options) {
            $migrator->rollback($options);
        });
    }
}
