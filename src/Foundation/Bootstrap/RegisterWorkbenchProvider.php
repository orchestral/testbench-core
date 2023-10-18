<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Workbench\WorkbenchServiceProvider as FallbackServicePorvider;
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
        $app->register(
            class_exists(WorkbenchServiceProvider::class)
                ? WorkbenchServiceProvider::class
                : FallbackServicePorvider::class
        );
    }
}
