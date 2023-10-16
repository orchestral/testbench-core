<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Workbench\Bootstrap\DiscoverRoutes;

/**
 * @internal
 */
class StartWorkbench extends \Orchestra\Testbench\Foundation\Bootstrap\StartWorkbench
{
    /**
     * Load Workbench providers.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    protected function loadWorkbenchProviders(Application $app): void
    {
        if (class_exists(DiscoverRoutes::class)) {
            (new DiscoverRoutes())->bootstrap($app);
        }
    }
}
