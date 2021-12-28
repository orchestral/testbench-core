<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
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

        $this->defineRoutes($this->app['router']);

        $this->app['router']->middleware('web')
            ->group(fn ($router) => $this->defineWebRoutes($router));

        if (method_exists($this, 'parseTestMethodAnnotations')) {
            $this->parseTestMethodAnnotations($this->app, 'define-route');
        }

        $this->app['router']->getRoutes()->refreshNameLookups();
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     *
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
     *
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
     *
     * @return void
     */
    protected function defineCacheRoutes(string $route)
    {
        $files = new Filesystem();

        $time = time();

        $files->put(
            base_path("routes/testbench-{$time}.php"), $route
        );

        Application::create(
            $this->getBasePath(),
        )->make(Kernel::class)->call('route:cache');
<<<<<<< HEAD

        if (isset($this->app)) {
            $this->reloadApplication();
        }
=======
>>>>>>> 6.x

        $this->assertTrue(
            $files->exists(base_path('bootstrap/cache/routes-v7.php'))
        );

<<<<<<< HEAD
=======
        if (isset($this->app)) {
            $this->reloadApplication();
        }

        $this->requireApplicationCachedRoutes($files);
    }

    /**
     * Require application cached routes.
     */
    protected function requireApplicationCachedRoutes(Filesystem $files): void
    {
>>>>>>> 6.x
        $this->afterApplicationCreated(function () {
            require $this->app->getCachedRoutesPath();
        });

        $this->beforeApplicationDestroyed(function () use ($files) {
            $files->delete(
                base_path('bootstrap/cache/routes-v7.php'),
                ...$files->glob(base_path('routes/testbench-*.php'))
            );

            sleep(1);
        });
    }
}
