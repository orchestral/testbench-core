<?php

namespace Orchestra\Testbench\Foundation\Console\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

use function Orchestra\Testbench\join_paths;

class SyncTestbenchCachedRoutes
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $app->make('router');

        collect(glob($app->basePath(join_paths('routes', 'testbench-*.php'))))
            ->each(static function ($routeFile) use ($app, $router) {
                require $routeFile;
            });

    }
}
