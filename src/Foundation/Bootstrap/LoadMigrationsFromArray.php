<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use function Orchestra\Testbench\transform_relative_path;

final class LoadMigrationsFromArray
{
    /**
     * The migrations.
     *
     * @var bool|array<int, string>
     */
    public $migrations;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  bool|array<int, string>  $migrations
     */
    public function __construct($migrations)
    {
        $this->migrations = $migrations;
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
            \is_array($this->migrations) ? $this->migrations : []
        )->when(
            $this->includesDefaultMigrations($app),
            fn ($migrations) => $migrations->push($app->basePath('migrations'))
        )->filter(fn ($migration) => \is_string($migration))
            ->transform(fn ($migration) => transform_relative_path($migration, $app->basePath()))
            ->all();

        $this->callAfterResolvingMigrator($app, function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }

    /**
     * Setup an after resolving listener, or fire immediately if already resolved.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  callable  $callback
     * @return void
     */
    protected function callAfterResolvingMigrator($app, $callback)
    {
        /** @phpstan-ignore-next-line */
        $app->afterResolving('migrator', $callback);

        if ($app->resolved('migrator')) {
            $callback($app->make('migrator'), $app);
        }
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
            && Env::get('TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS') !== true;
    }
}
