<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Env;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\laravel_migration_path;
use function Orchestra\Testbench\transform_relative_path;
use function Orchestra\Testbench\workbench;

/**
 * @internal
 */
final class LoadMigrationsFromArray
{
    /**
     * The migrations.
     *
     * @var string|array<int, string>|bool
     */
    public $migrations;

    /**
     * The seeders.
     *
     * @var class-string|array<int, class-string>|bool
     */
    public $seeders;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  string|array<int, string>|bool  $migrations
     * @param  class-string|array<int, class-string>|bool  $seeders
     */
    public function __construct($migrations = [], $seeders = false)
    {
        $this->migrations = $migrations;
        $this->seeders = $seeders;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        if ($this->seeders !== false) {
            $this->bootstrapSeeders($app);
        }

        if ($this->migrations !== false) {
            $this->bootstrapMigrations($app);
        }
    }

    /**
     * Bootstrap seeders.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function bootstrapSeeders(Application $app): void
    {
        $app->make(EventDispatcher::class)
            ->listen(DatabaseRefreshed::class, function (DatabaseRefreshed $event) use ($app) {
                if (\is_bool($this->seeders) && $this->seeders === false) {
                    return;
                }

                Collection::make(Arr::wrap($this->seeders))
                    ->flatten()
                    ->filter(static function ($seederClass) {
                        return ! \is_null($seederClass) && class_exists($seederClass);
                    })->each(static function ($seederClass) use ($app) {
                        $app->make(ConsoleKernel::class)->call('db:seed', [
                            '--class' => $seederClass,
                        ]);
                    });
            });
    }

    /**
     * Bootstrap migrations.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function bootstrapMigrations(Application $app): void
    {
        $paths = Collection::make(
            ! \is_bool($this->migrations) ? Arr::wrap($this->migrations) : []
        )->when($this->includesDefaultMigrations($app), static function ($migrations) {
            return $migrations->push(laravel_migration_path());
        })->filter(static function ($migration) {
            return \is_string($migration);
        })->transform(static function ($migration) use ($app) {
            return transform_relative_path($migration, $app->basePath());
        })->all();

        after_resolving($app, 'migrator', static function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                /** @var \Illuminate\Database\Migrations\Migrator $migrator */
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
        return
            workbench()['install'] === true
            && Env::get('TESTBENCH_WITHOUT_DEFAULT_MIGRATIONS') !== true
            && is_dir(laravel_migration_path());
    }
}
