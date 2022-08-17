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
     * @param  bool                         $rollback
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function loadMigrationsFrom($paths, bool $rollback = true): void
    {
        $options = \is_array($paths) ? $paths : ['--path' => $paths];

        if (isset($options['--realpath']) && ! \is_bool($options['--realpath'])) {
            throw new InvalidArgumentException('Expect --realpath to be a boolean.');
        }

        $options['--realpath'] = true;

        $migrator = new MigrateProcessor($this, $options);
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);

        if ($rollback) {
            $this->beforeApplicationDestroyed(fn () => $migrator->rollback());
        }
    }
}
