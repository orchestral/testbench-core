<?php

namespace Orchestra\Testbench\Workbench;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        static::authenticationRoutes();

        $this->app->make(HttpKernel::class)->pushMiddleware(Http\Middleware\CatchDefaultRoute::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\CreateSqliteDbCommand::class,
                Console\DropSqliteDbCommand::class,
                Console\InstallCommand::class,
            ]);
        }
    }

    /**
     * Provide the authentication routes for Testbench.
     *
     * @return void
     */
    public static function authenticationRoutes()
    {
        Route::group(array_filter([
            'prefix' => '_workbench',
            'middleware' => 'web',
        ]), function (Router $router) {
            $router->get(
                '/', [Http\Controllers\WorkbenchController::class, 'start']
            )->name('workbench.start');

            $router->get(
                '/login/{userId}/{guard?}', [Http\Controllers\WorkbenchController::class, 'login']
            )->name('workbench.login');

            $router->get(
                '/logout/{guard?}', [Http\Controllers\WorkbenchController::class, 'logout']
            )->name('workbench.logout');

            $router->get(
                '/user/{guard?}', [Http\Controllers\WorkbenchController::class, 'user']
            )->name('workbench.user');
        });
    }
}
