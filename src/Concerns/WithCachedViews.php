<?php

namespace Orchestra\Testbench\Concerns;

use function Orchestra\Testbench\artisan;

trait WithCachedViews
{
    /**
     * Cache views before each test.
     *
     * @before
     *
     * @return void
     */
    protected function setUpWithCachedViews(): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        artisan($app, 'view:cache');

        if ($app->bound('view')) {
            $app->make('view')->flushFinderCache();
        }
    }

    /**
     * Clear cached views after each test.
     *
     * @after
     *
     * @return void
     */
    protected function tearDownWithCachedViews(): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        artisan($app, 'view:clear');
    }
}
