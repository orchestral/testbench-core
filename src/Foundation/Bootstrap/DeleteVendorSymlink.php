<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Foundation\Actions\DeleteVendorSymlink as Action;

/**
 * @internal
 *
 * @deprecated
 * 
 * @codeCoverageIgnore
 */
final class DeleteVendorSymlink
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        (new Action)->handle($app);
    }
}
