<?php

namespace Orchestra\Testbench\Foundation\Actions;

use Illuminate\Contracts\Foundation\Application;

/**
 * @internal
 */
final class DeleteVendorSymlink
{
    /**
     * Execute the command.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function handle(Application $app): void
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
