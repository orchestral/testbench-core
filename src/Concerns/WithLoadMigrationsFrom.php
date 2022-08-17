<?php

namespace Orchestra\Testbench\Concerns;

use InvalidArgumentException;
use Orchestra\Testbench\Database\MigrateProcessor;

trait WithLoadMigrationsFrom
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @param  string|array<string, mixed>  $paths
     *
     * @return void
     */
    protected function loadMigrationsFrom($paths): void
    {
        $migrator = $this->loadMigrationsWithoutRollbackFrom($paths);

        $this->beforeApplicationDestroyed(static function () use ($migrator) {
            $migrator->rollback();
        });
    }

    /**
     * Define hooks to migrate the database before each test without rollback after.
     *
     * @param  string|array<string, mixed>  $paths
     *
     * @return MigrateProcessor
     */
    protected function loadMigrationsWithoutRollbackFrom($paths): MigrateProcessor
    {
        $options = \is_array($paths) ? $paths : ['--path' => $paths];

        if (isset($options['--realpath']) && ! \is_bool($options['--realpath'])) {
            throw new InvalidArgumentException('Expect --realpath to be a boolean.');
        }

        $options['--realpath'] = true;

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        return $migrator;
    }
}
