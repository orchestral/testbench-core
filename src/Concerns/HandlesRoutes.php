<?php

namespace Orchestra\Testbench\Concerns;

trait HandlesRoutes
{
    protected function setUpApplicationRoutes(): void
    {
        if (! $this->app->eventsAreCached()) {
            $this->defineRoutes($this->app['router']);

            $this->parseTestMethodAnnotations($this->app, 'define-route');

            $this->app['router']->getRoutes()->refreshNameLookups();
        }
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
     * Define cache routes setup.
     *
     * @param  string  $route
     *
     * @return void
     */
    protected function defineCacheRoutes(string $route)
    {
        $files = $this->app['files'];

        $files->put(
            \base_path('routes/testbench.php'), $route
        );

        $this->artisan('route:cache')->run();

        $this->reloadApplication();

        $this->assertTrue(
            $files->exists(\base_path('bootstrap/cache/routes-v7.php'))
        );

        $this->requireApplicationCachedRoutes();

        $this->beforeApplicationDestroyed(function () use ($files) {
            $files->delete(
                \base_path('bootstrap/cache/routes-v7.php'),
                \base_path('routes/testbench.php')
            );
        });
    }

    /**
     * Require application cached routes.
     */
    protected function requireApplicationCachedRoutes(): void
    {
        $this->app->booted(function () {
            require $this->app->getCachedRoutesPath();
        });
    }

    /**
     * Parse test method annotations.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  string  $name
     */
    abstract protected function parseTestMethodAnnotations($app, string $name): void;
}
