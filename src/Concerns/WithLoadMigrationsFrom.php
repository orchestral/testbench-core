<?php

namespace Orchestra\Testbench\Concerns;

use InvalidArgumentException;
use Orchestra\Testbench\Database\MigrateProcessor;
use Orchestra\Testbench\Exceptions\ApplicationNotAvailableException;

trait WithLoadMigrationsFrom
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @param  string|array<string, mixed>  $paths
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function loadMigrationsFrom($paths): void
    {
        $this->loadMigrationsWithoutRollbackFrom($paths);

        $this->beforeApplicationDestroyed(function () use ($paths) {
            (new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths)))->rollback();
        });
    }

    /**
     * Define hooks to migrate the database before each test without rollback after.
     *
     * @param  string|array<string, mixed>  $paths
     * @return void
     */
    protected function loadMigrationsWithoutRollbackFrom($paths): void
    {
        if (\is_null($this->app)) {
            throw ApplicationNotAvailableException::make(__METHOD__);
        }

        $migrator = new MigrateProcessor($this, $this->resolvePackageMigrationsOptions($paths));
        $migrator->up();

        $this->resetApplicationArtisanCommands($this->app);
    }

    /**
     * Resolve Package Migrations Artisan command options.
     *
     * @param  string|array<string, mixed>  $paths
     * @return array
     */
    protected function resolvePackageMigrationsOptions($paths = []): array
    {
        $options = \is_array($paths) ? $paths : ['--path' => $paths];

        if (isset($options['--realpath']) && ! \is_bool($options['--realpath'])) {
            throw new InvalidArgumentException('Expect --realpath to be a boolean.');
        }

        $options['--realpath'] = true;

        return $options;
    }
}
