<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CollisionTestCommand;
use Orchestra\Testbench\Contracts\Config as ConfigContract;

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

        app(EventDispatcher::class)
            ->listen(DatabaseRefreshed::class, function () {
                /** @var \Orchestra\Testbench\Foundation\Config $config */
                $config = $this->app->bound(ConfigContract::class)
                    ? $this->app->make(ConfigContract::class)
                    : new Config();

                /** @var class-string|array<int, class-string>|bool $seederClasses */
                $seederClasses = $config->get('seeders') ?? false;

                if (\is_bool($seederClasses) && $seederClasses === false) {
                    return;
                }

                collect(Arr::wrap($seederClasses))
                    ->filter(function ($seederClass) {
                        return ! \is_null($seederClass) && class_exists($seederClass);
                    })
                    ->each(function ($seederClass) {
                        app(ConsoleKernel::class)->call('db:seed', [
                            '--class' => $seederClass,
                        ]);
                    });
            });

        Application::authenticationRoutes();

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
