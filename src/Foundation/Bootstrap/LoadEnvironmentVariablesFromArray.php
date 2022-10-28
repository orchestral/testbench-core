<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Store\StringStore;
use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;

final class LoadEnvironmentVariablesFromArray
{
    /**
     * The environment variables.
     *
     * @var array
     */
    public $environmentVariables;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  array  $environmentVariables
     */
    public function __construct(array $environmentVariables)
    {
        $this->environmentVariables = $environmentVariables;
    }

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $this->createDotenvFromString()->load();
    }

    /**
     * Create a Dotenv instance.
     */
    protected function createDotenvFromString(): Dotenv
    {
        return new Dotenv(
            new StringStore(implode("\n", $this->environmentVariables ?? [])),
            new Parser(),
            new Loader(),
            Env::getRepository()
        );
    }
}
