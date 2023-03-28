<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CollisionTestCommand;

class TestbenchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! file_exists($this->app->databasePath('database.sqlite')) && config('database.default') === 'sqlite') {
            config(['database.default' => 'testing']);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                $this->isCollisionDependenciesInstalled()
                    ? Console\TestCommand::class
                    : Console\TestFallbackCommand::class,
            ]);
        }
    }

    /**
     * Register migrations from.
     *
     * @param  array  $paths
     * @return void
     */
    public function registerMigrationsFrom(array $paths): void
    {
        if (file_exists($this->app->basePath('migrations'))) {
            array_push($paths, $this->app->basePath('migrations'));
        }

        $this->loadMigrationsFrom($paths);
    }

    /**
     * Check if the parallel dependencies are installed.
     *
     * @return bool
     */
    protected function isCollisionDependenciesInstalled(): bool
    {
        return class_exists(CollisionTestCommand::class);
    }
}
