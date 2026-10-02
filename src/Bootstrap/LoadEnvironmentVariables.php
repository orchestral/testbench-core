<?php

namespace Orchestra\Testbench\Bootstrap;

use Dotenv\Dotenv;
use Illuminate\Support\Facades\ParallelTesting;
use Orchestra\Sidekick\Env;

use function Orchestra\Sidekick\Filesystem\join_paths;
use function Orchestra\Testbench\in_parallel_testing;

/**
 * @internal
 */
final class LoadEnvironmentVariables extends \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables
{
    /** {@inheritDoc} */
    #[\Override]
    protected function createDotenv($app)
    {
        $environmentFile = implode('_', array_filter([
            $app->environmentFile(),
            in_parallel_testing() ? 'test_'.ParallelTesting::token() : null,
        ]));

        /** @phpstan-ignore method.notFound, method.notFound */
        if (! is_file(join_paths($app->environmentPath(), $environmentFile))) {
            return Dotenv::create(
                Env::getRepository(), (string) realpath(join_paths(__DIR__, 'stubs')), '.env.testbench'
            );
        }

        return parent::createDotenv($app);
    }
}
