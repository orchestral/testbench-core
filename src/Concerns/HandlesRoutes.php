<?php

namespace Orchestra\Testbench\Concerns;

trait HandlesRoutes
{
    protected function setupApplicationRoutes(): void
    {
        if (! $this->app->eventsAreCached()) {
            $this->defineRoutes($this->app['router']);

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
}
