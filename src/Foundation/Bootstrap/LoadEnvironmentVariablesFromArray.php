<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Store\StringStore;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Foundation\Env;

/**
 * @internal
 */
final class LoadEnvironmentVariablesFromArray
{
    /**
     * The environment variables.
     *
     * @var array<int, mixed>
     */
    public $environmentVariables;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  array<int, mixed>  $environmentVariables
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
            new StringStore(implode(PHP_EOL, $this->environmentVariables)),
            new Parser(),
            new Loader(),
            Env::getRepository()
        );
    }
}
