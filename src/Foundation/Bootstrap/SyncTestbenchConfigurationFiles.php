<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Orchestra\Testbench\Foundation\Console\Concerns\CopyTestbenchFiles;

use function Orchestra\Testbench\package_path;

class SyncTestbenchConfigurationFiles
{
    use CopyTestbenchFiles;

    /**
     * The environment file name.
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $filesystem = new Filesystem;

        if (! $filesystem->exists($app->basePath('testbench.yaml'))) {
            $this->copyTestbenchConfigurationFile($app, $filesystem, package_path());
        }

        if (! $filesystem->exists($app->basePath('.env'))) {
            $this->copyTestbenchDotEnvFile($app, $filesystem, package_path());
        }

        $app->terminating(function () {
            $this->handleTerminatingConsole();
        });
    }
}
