<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use Orchestra\Testbench\Foundation\Application;

trait HandlesRoutes
{
    /**
     * Setup routes requirements.
     */
    protected function setUpApplicationRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        /** @var \Illuminate\Routing\Router $router */
        $router = $this->app['router'];

        $this->defineRoutes($router);

        $router->middleware('web')
            ->group(function ($router) {
                $this->defineWebRoutes($router);
            });

        if (method_exists($this, 'parseTestMethodAnnotations')) {
            $this->parseTestMethodAnnotations($this->app, 'define-route');
        }

        $router->getRoutes()->refreshNameLookups();
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
