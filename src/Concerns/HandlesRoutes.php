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
