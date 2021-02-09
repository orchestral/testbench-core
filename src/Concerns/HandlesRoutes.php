<?php

namespace Orchestra\Testbench\Concerns;

trait HandlesRoutes
{
    protected function setUpApplicationRoutes(): void
    {
        if ($this->app->eventsAreCached()) {
            return;
        }

        $this->defineRoutes($this->app['router']);

        $this->app['router']->middleware('web')
            ->group(function ($router) {
                $this->defineWebRoutes($router);
            });

        if (\method_exists($this, 'parseTestMethodAnnotations')) {
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
}
