<?php

namespace Orchestra\Testbench\Console;

use Dotenv\Dotenv;
use Dotenv\Loader\Loader;
use Dotenv\Parser\Parser;
use Dotenv\Store\StringStore;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Env;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * @phpstan-type TConfig array{laravel: string|null, env: array|null, providers: array|null, dont-discover: array|null}
 */
class Commander
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * List of configurations.
     *
     * @var TConfig
     */
    protected $config = [
        'laravel' => null,
        'env' => [],
        'providers' => [],
        'dont-discover' => [],
    ];

    /**
     * Working path.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Construct a new Commander.
     *
     * @param  TConfig  $config
     * @param  string  $workingPath
     */
    public function __construct(array $config, string $workingPath)
    {
        $this->config = $config;
        $this->workingPath = $workingPath;
    }

    /**
     * Get Application base path.
     *
     * @return string
     */
    public static function applicationBasePath()
    {
        return Application::applicationBasePath();
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
        if (! $this->app instanceof LaravelApplication) {
            $this->createSymlinkToVendorPath();

            $this->app = Application::create($this->getBasePath(), $this->resolveApplicationCallback(), [
                'extra' => [
                    'providers' => $this->config['providers'] ?? [],
                    'dont-discover' => $this->config['dont-discover'] ?? [],
                ],
            ]);
        }

        return $this->app;
    }

    /**
     * Resolve application implementation.
     *
     * @return \Closure
     */
    protected function resolveApplicationCallback()
    {
        return function ($app) {
            $this->createDotenv()->load();

            $app->register(TestbenchServiceProvider::class);
        };
    }

    /**
     * Create a Dotenv instance.
     */
    protected function createDotenv(): Dotenv
    {
        $laravelBasePath = $this->getBasePath();

        if (file_exists($laravelBasePath.'/.env')) {
            return Dotenv::create(
                Env::getRepository(), $laravelBasePath.'/', '.env'
            );
        }

        return new Dotenv(
            new StringStore(implode("\n", $this->config['env'] ?? [])),
            new Parser(),
            new Loader(),
            Env::getRepository()
        );
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
            return tap(str_replace('./', $this->workingPath.'/', $laravelBasePath), static function ($path) {
                $_ENV['APP_BASE_PATH'] = $path;
            });
        }

        return static::applicationBasePath();
    }

    /**
     * Create symlink on vendor path.
     *
     * @deprecated Use `Orchestra\Testbench\Foundation\Application::createVendorSymlink()` insteads
     */
    protected function createSymlinkToVendorPath(): void
    {
        Application::createVendorSymlink(
            $this->getBasePath(),
            $this->workingPath.'/vendor'
        );
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $kernel = 'Orchestra\Testbench\Console\Kernel';

        if (file_exists($app->basePath('app/Console/Kernel.php')) && class_exists('App\Console\Kernel')) {
            $kernel = 'App\Console\Kernel';
        }

        $app->singleton('Illuminate\Contracts\Console\Kernel', $kernel);
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $kernel = 'Orchestra\Testbench\Http\Kernel';

        if (file_exists($app->basePath('app/Http/Kernel.php')) && class_exists('App\Http\Kernel')) {
            $kernel = 'App\Http\Kernel';
        }

        $app->singleton('Illuminate\Contracts\Http\Kernel', $kernel);
    }

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $error
     * @return int
     */
    protected function handleException(OutputInterface $output, Throwable $error)
    {
        $laravel = $this->laravel();

        tap($laravel->make(ExceptionHandler::class), static function ($handler) use ($error, $output) {
            $handler->report($error);
            $handler->renderForConsole($output, $error);
        });

        return 1;
    }
}
