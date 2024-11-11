<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;

/**
 * @internal
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
        tap($app->basePath('vendor'), static function ($appVendorPath) {
            if (windows_os() && is_dir($appVendorPath) && readlink($appVendorPath) !== $appVendorPath) {
                @rmdir($appVendorPath);
            } elseif (is_link($appVendorPath)) {
                @unlink($appVendorPath);
            }

            clearstatcache(false, \dirname($appVendorPath));
        });
    }
}
