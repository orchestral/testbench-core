<?php

namespace Orchestra\Testbench\Console;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Store\StringStore;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

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

        $input = new ArgvInput();
        $output = new ConsoleOutput();

        try {
            $status = $kernel->handle($input, $output);
        } catch (Throwable $error) {
            $status = $this->handleException($output, $error);
        }

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
            $this->createSymlinkToVendorPath();

            $this->app = $this->createApplication();
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
        return $this->config['dont-discover'] ?? [];
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
            new StringStore(\implode("\n", $this->config['env'] ?? [])),
            new Parser(),
            new Loader(),
            Env::getRepository()
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

    /**
     * Create symlink on vendor path.
     */
    protected function createSymlinkToVendorPath(): void
    {
        \tap($this->resolveApplication(), function ($laravel) {
            $filesystem = new Filesystem();

            $filesystem->delete($laravelVendorPath = $laravel->basePath('vendor'));
            $filesystem->link($this->workingPath.'/vendor', $laravelVendorPath);

            $laravel->flush();
        });
    }

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $error
     *
     * @return int
     */
    protected function handleException(OutputInterface $output, Throwable $error)
    {
        $laravel = $this->laravel();

        \tap($laravel->make(ExceptionHandler::class), function ($handler) use ($error, $output) {
            $handler->report($error);
            $handler->renderForConsole($output, $error);
        });

        return 1;
    }
}
