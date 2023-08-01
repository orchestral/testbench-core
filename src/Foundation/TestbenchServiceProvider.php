<?php

namespace Orchestra\Testbench\Foundation;

use Composer\InstalledVersions;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as CollisionTestCommand;

class TestbenchServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $workingPath = \defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : null;

        AboutCommand::add('Testbench', fn () => array_filter([
            'Core Version' => InstalledVersions::getPrettyVersion('orchestra/testbench-core'),
            'Dusk Version' => InstalledVersions::isInstalled('orchestra/testbench-dusk') ? InstalledVersions::getPrettyVersion('orchestra/testbench-dusk') : null,
            'Skeleton Path' => str_replace($workingPath, '', $this->app->basePath()),
            'Version' => InstalledVersions::isInstalled('orchestra/testbench') ? InstalledVersions::getPrettyVersion('orchestra/testbench') : null,
        ]));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        /** @var \Orchestra\Testbench\Foundation\Config $config */
        $config = app('testbench.config');

        app(EventDispatcher::class)
            ->listen(DatabaseRefreshed::class, function () use ($config) {
                /** @var class-string|null $seederClass */
                $seederClass = $config->get('seeder');

                if (! \is_null($seederClass) && class_exists($seederClass)) {
                    app(ConsoleKernel::class)->call('db:seed', [
                        '--class' => $seederClass,
                    ]);
                }
            });

        Route::group(array_filter([
            'prefix' => '_testbench',
            'middleware' => 'web',
        ]), function (Router $router) {
            $router->get(
                '/login/{userId}/{guard?}', [Http\Controllers\UserController::class, 'login']
            )->name('testbench.login');

            $router->get(
                '/logout/{guard?}', [Http\Controllers\UserController::class, 'logout']
            )->name('testbench.logout');

            $router->get(
                '/user/{guard?}', [Http\Controllers\UserController::class, 'user']
            )->name('testbench.user');
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                $this->isCollisionDependenciesInstalled()
                    ? Console\TestCommand::class
                    : Console\TestFallbackCommand::class,
                Console\CreateSqliteDbCommand::class,
                Console\DropSqliteDbCommand::class,
                Console\DevToolCommand::class,
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
