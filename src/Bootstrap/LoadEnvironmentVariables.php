<?php

namespace Orchestra\Testbench\Bootstrap;

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
        /** @phpstan-ignore-next-line */
        if (! file_exists(implode(DIRECTORY_SEPARATOR, [$app->environmentPath(), $app->environmentFile()]))) {
            $this->setEnvironmentFilePath($app, '.env.testbench');
        }

        return parent::createDotenv($app);
    }
}
