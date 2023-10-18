<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Workbench\WorkbenchServiceProvider;

/**
 * @internal
 */
final class RegisterWorkbenchProvider
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        if (class_exists(WorkbenchServiceProvider::class)) {
            $app->register(WorkbenchServiceProvider::class);
        }
    }
}
