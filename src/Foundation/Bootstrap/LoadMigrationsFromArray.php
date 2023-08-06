<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\transform_relative_path;
use function Orchestra\Testbench\workbench;

final class LoadMigrationsFromArray
{
    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  string|array<int, string>|bool  $migrations
     */
    public function __construct(
        public string|bool|array $migrations
    ) {
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        if ($this->migrations === false) {
            return;
        }

        $paths = Collection::make(
            ! \is_bool($this->migrations) ? Arr::wrap($this->migrations) : []
        )->when(
            $this->includesDefaultMigrations($app),
            fn ($migrations) => $migrations->push($app->basePath('migrations'))
        )->filter(fn ($migration) => \is_string($migration))
            ->transform(fn ($migration) => transform_relative_path($migration, $app->basePath()))
            ->all();

        after_resolving($app, 'migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }

    /**
     * Determine whether default migrations should be included.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return bool
     */
    protected function includesDefaultMigrations($app): bool
    {
        return is_dir($app->basePath('migrations'))
            && (
                workbench()['install'] === true
                && Env::get('TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS') !== true
            );
    }
}
