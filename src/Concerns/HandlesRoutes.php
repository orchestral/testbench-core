<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use Orchestra\Testbench\Attributes\DefineRoute;
use Orchestra\Testbench\Features\TestingFeature;
use Orchestra\Testbench\Foundation\Application;

use function Orchestra\Testbench\refresh_router_lookups;

/**
 * @internal
 */
trait HandlesRoutes
{
    /**
     * Setup routes requirements.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function setUpApplicationRoutes($app): void
    {
        if ($app->routesAreCached()) {
            return;
        }

        /** @var \Illuminate\Routing\Router $router */
        $router = $app['router'];

        TestingFeature::run(
            $this,
            function () use ($router) {
                $this->defineRoutes($router);

                $router->middleware('web')
                    ->group(function ($router) {
                        $this->defineWebRoutes($router);
                    });
            },
            function () use ($app, $router) {
                $this->parseTestMethodAnnotations($app, 'define-route', function ($method) use ($router) {
                    $this->{$method}($router);
                });
            },
            function () use ($app) {
                $this->parseTestMethodAttributes($app, DefineRoute::class);
            }
        );

        refresh_router_lookups($router);
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        // Define routes.
    }

    /**
     * Define web routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineWebRoutes($router)
    {
        // Define routes.
    }

    /**
     * Define cache routes setup.
     *
     * @param  string  $route
     * @return void
     */
    protected function defineCacheRoutes(string $route)
    {
        $files = new Filesystem();

        $time = time();

        $laravel = Application::create(static::applicationBasePath());

        $files->put(
            $laravel->basePath("routes/testbench-{$time}.php"), $route
        );

        $laravel->make(Kernel::class)->call('route:cache');

        $this->assertTrue(
            $files->exists(base_path('bootstrap/cache/routes-v7.php'))
        );

        if ($this->app instanceof LaravelApplication) {
            $this->reloadApplication();
        }

        $this->requireApplicationCachedRoutes($files);
    }

    /**
     * Require application cached routes.
     */
    protected function requireApplicationCachedRoutes(Filesystem $files): void
    {
        $this->afterApplicationCreated(function () {
            if ($this->app instanceof LaravelApplication) {
                require $this->app->getCachedRoutesPath();
            }
        });

        $this->beforeApplicationDestroyed(function () use ($files) {
            if ($this->app instanceof LaravelApplication) {
                $files->delete(
                    base_path('bootstrap/cache/routes-v7.php'),
                    ...$files->glob(base_path('routes/testbench-*.php'))
                );
            }

            sleep(1);
        });
    }
}
