<?php

namespace Orchestra\Testbench\Bootstrap;

use Dotenv\Dotenv;
use Orchestra\Sidekick\Env;

use function Orchestra\Sidekick\Filesystem\join_paths;

/**
 * @internal
 */
final class LoadEnvironmentVariables extends \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables
{
    /** {@inheritDoc} */
    #[\Override]
    protected function createDotenv($app)
    {
        /** @var \Illuminate\Contracts\Foundation\Application&\Illuminate\Foundation\Application $app */
        if (! is_file(join_paths($app->environmentPath(), $app->environmentFile()))) {
            return Dotenv::create(
                Env::getRepository(), (string) realpath(join_paths(__DIR__, 'stubs')), '.env.testbench'
            );
        }

        return parent::createDotenv($app);
    }
}
