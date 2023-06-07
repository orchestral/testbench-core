<?php

namespace Orchestra\Testbench\Bootstrap;

use Dotenv\Dotenv;
use Illuminate\Support\Env;

final class LoadEnvironmentVariables extends \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables
{
    /**
     * Create a Dotenv instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return \Dotenv\Dotenv
     */
    protected function createDotenv($app)
    {
        if (! file_exists(implode('/', [$app->environmentPath(), $app->environmentFile()]))) {
            $environmentPath = (string) realpath(__DIR__.'/stubs');

            return Dotenv::create(
                Env::getRepository(), $environmentPath, '.env.testbench'
            );
        }

        return parent::createDotenv($app);
    }
}
