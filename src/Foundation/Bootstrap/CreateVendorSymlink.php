<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

use function Orchestra\Testbench\join_paths;
use function Orchestra\Testbench\laravel_vendor_exists;

/**
 * @internal
 */
final class CreateVendorSymlink
{
    /**
     * The project working path.
     *
     * @var string
     */
    public $workingPath;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  string  $workingPath
     */
    public function __construct(string $workingPath)
    {
        $this->workingPath = $workingPath;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $filesystem = new Filesystem;

        $appVendorPath = $app->basePath('vendor');

        $vendorLinkCreated = false;

        if (! laravel_vendor_exists($app, $this->workingPath)) {
            if ($filesystem->exists($app->bootstrapPath(join_paths('cache', 'packages.php')))) {
                $filesystem->delete($app->bootstrapPath(join_paths('cache', 'packages.php')));
            }

            $this->deleteVendorSymlink($app);

            try {
                $filesystem->link($this->workingPath, $appVendorPath);

                $vendorLinkCreated = true;
            } catch (ErrorException $e) {
                //
            }
        }

        $app->flush();

        $app->instance('TESTBENCH_VENDOR_SYMLINK', $vendorLinkCreated);
    }

    /**
     * Safely remove symlink for Unix & Windows environment.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function deleteVendorSymlink(Application $app): void
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
