<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use function Orchestra\Testbench\after_resolving;
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

        /** @var string|array<int, string>|bool $migrations */
        $migrations = ! \is_bool($this->migrations) ? $this->migrations : [];

        $paths = Collection::make(Arr::wrap($migrations))->when(
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
            && Env::get('TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS') !== true;
    }
}
