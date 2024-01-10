<?php

namespace Orchestra\Testbench\Bootstrap;

use Dotenv\Dotenv;
use Orchestra\Testbench\Foundation\Env;

use function Illuminate\Filesystem\join_paths;

/**
 * @internal
 */
final class LoadEnvironmentVariables extends \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables
{
    /**
     * Create a Dotenv instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Dotenv\Dotenv
     */
    #[\Override]
    protected function createDotenv($app)
    {
        if (! file_exists(join_paths($app->environmentPath(), $app->environmentFile()))) {
            return Dotenv::create(
                Env::getRepository(), join_paths((string) realpath(__DIR__), 'stubs'), '.env.testbench'
            );
        }

        return parent::createDotenv($app);
    }
}
