<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Store\StringStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;

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

        // config([
        //     ray()->pass(Collection::make([
        //         'APP_KEY' => ['app.key' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF'],
        //         'APP_DEBUG' => ['app.debug' => true],
        //         'DB_CONNECTION' => \defined('TESTBENCH_DUSK') ? ['database.default' => 'testing'] : null,
        //     ])->filter()
        //     ->reject(function ($config, $key) {
        //         return !is_null(Env::get($key));
        //     })->values()
        //     ->all())
        // ]);
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
