<?php

namespace Orchestra\Testbench\Console;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Console\Dotenv\StringStore;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Commander
{
    use CreatesApplication {
        resolveApplication as protected resolveApplicationFromTrait;
        getBasePath as protected getBasePathFromTrait;
    }

    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * List of configurations.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Working path.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Construct a new Commander.
     *
     * @param  array  $config
     * @param  string  $workingPath
     */
    public function __construct(array $config, string $workingPath)
    {
        $this->config = $config;
        $this->workingPath = $workingPath;
    }

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $laravel = $this->laravel();
        $kernel = $laravel->make(ConsoleKernel::class);

        $status = $kernel->handle(
            $input = new ArgvInput(), new ConsoleOutput()
        );

        $kernel->terminate($input, $status);

        exit($status);
    }

    /**
     * Create Laravel application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function laravel()
    {
        if (! $this->app) {
            $this->app = \tap($this->createApplication(), function ($laravel) {
                $filesystem = new Filesystem();

                if (! $filesystem->isDirectory($laravelVendorPath = $laravel->basePath('vendor'))) {
                    $filesystem->link($this->workingPath.'/vendor', $laravelVendorPath);
                }
            });
        }

        return $this->app;
    }

    /**
     * Ignore package discovery from.
     *
     * @return array
     */
    public function ignorePackageDiscoveriesFrom()
    {
        return [];
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return $this->config['providers'] ?? [];
    }

    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        return \tap($this->resolveApplicationFromTrait(), function () {
            $this->createDotenv()->load();
        });
    }

    /**
     * Create a Dotenv instance.
     */
    protected function createDotenv(): Dotenv
    {
        $laravelBasePath = $this->getBasePath();

        if (\file_exists($laravelBasePath.'/.env')) {
            return Dotenv::create(
                Env::getRepository(), $laravelBasePath.'/', '.env'
            );
        }

        return (new Dotenv(
            new Loader(),
            Env::getRepository(),
            new StringStore(implode("\n", $this->config['env'] ?? []))
        ));
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        $laravelBasePath = $this->config['laravel'] ?? null;

        if (! \is_null($laravelBasePath)) {
            return \str_replace('./', $this->workingPath.'/', $laravelBasePath);
        }

        return $this->getBasePathFromTrait();
    }
}
