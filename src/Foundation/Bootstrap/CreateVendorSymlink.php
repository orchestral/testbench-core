<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

use function Orchestra\Testbench\join_paths;

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

        if (! $this->vendorSymlinkExists($app)) {
            if ($filesystem->exists($app->bootstrapPath(join_paths('cache', 'packages.php')))) {
                $filesystem->delete($app->bootstrapPath(join_paths('cache', 'packages.php')));
            }

            if (is_link($appVendorPath)) {
                $filesystem->delete($appVendorPath);
            }

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
     * Determine if vendor symlink exists on the skeleton.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return bool
     */
    public function vendorSymlinkExists(Application $app): bool
    {
        $filesystem = new Filesystem;

        $appVendorPath = $app->basePath('vendor');

        return $filesystem->isFile(join_paths($appVendorPath, 'autoload.php')) &&
            $filesystem->hash(join_paths($appVendorPath, 'autoload.php')) === $filesystem->hash(join_paths($this->workingPath, 'autoload.php'));
    }
}
