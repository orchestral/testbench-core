<?php

namespace Orchestra\Testbench\Foundation;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CollisionTestCommand;

use function Orchestra\Testbench\package_path;

class TestbenchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        AboutCommand::add('Testbench', fn () => [
            'Core Version' => class_exists(InstalledVersions::class) ? InstalledVersions::getPrettyVersion('orchestra/testbench-core') : '<fg=yellow;options=bold>-</>',
            'Skeleton Path' => str_replace(package_path(), '', $this->app->basePath()),
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                $this->isCollisionDependenciesInstalled()
                    ? Console\TestCommand::class
                    : Console\TestFallbackCommand::class,
                Console\CreateSqliteDbCommand::class,
                Console\DevToolCommand::class,
                Console\DropSqliteDbCommand::class,
                Console\PurgeSkeletonCommand::class,
                Console\ServeCommand::class,
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
