<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;

use function Illuminate\Filesystem\join_paths;

/**
 * @internal
 */
final class CreateVendorSymlink
{
    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  string  $workingPath
     */
    public function __construct(
        protected readonly string $workingPath
    ) {
        //
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

        if (
            ! $filesystem->isFile(join_paths($appVendorPath, 'autoload.php')) ||
            $filesystem->hash(join_paths($appVendorPath, 'autoload.php')) !== $filesystem->hash(join_paths($this->workingPath, 'autoload.php'))
        ) {
            if ($filesystem->exists($app->bootstrapPath(join_paths('cache', 'packages.php')))) {
                $filesystem->delete($app->bootstrapPath(join_paths('cache', 'packages.php')));
            }

            if (is_link($appVendorPath)) {
                $filesystem->delete($appVendorPath);
            }

            try {
                $filesystem->link($this->workingPath, $appVendorPath);
            } catch (ErrorException) {
                //
            }
        }

        $app->flush();
    }
}
