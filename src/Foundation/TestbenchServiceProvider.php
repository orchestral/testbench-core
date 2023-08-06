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
     * Check if the parallel dependencies are installed.
     *
     * @return bool
     */
    protected function isCollisionDependenciesInstalled(): bool
    {
        return class_exists(CollisionTestCommand::class);
    }
}
