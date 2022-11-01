<?php

namespace Orchestra\Testbench\Console;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Arr;
use function Orchestra\Testbench\default_environment_variables;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Bootstrap\LoadEnvironmentVariablesFromArray;
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
            $laravelBasePath = $this->getBasePath();

            Application::createVendorSymlink(
                $laravelBasePath, $this->workingPath.'/vendor'
            );

            $hasEnvironmentFile = file_exists("{$laravelBasePath}/.env");

            $options = array_filter([
                'load_environment_variables' => $hasEnvironmentFile,
                'extra' => [
                    'providers' => Arr::get($this->config, 'providers', []),
                    'dont-discover' => Arr::get($this->config, 'dont-discover', []),
                ],
            ]);

            $this->app = Application::create(
                basePath: $this->getBasePath(),
                resolvingCallback: function ($app) use ($hasEnvironmentFile) {
                    if ($hasEnvironmentFile === false) {
                        (new LoadEnvironmentVariablesFromArray(
                            ! empty($this->config['env']) ? $this->config['env'] : default_environment_variables()
                        ))->bootstrap($app);
                    }

                    call_user_func($this->resolveApplicationCallback(), $app);
                },
                options: $options
            );
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
            $app->register(TestbenchServiceProvider::class);
        };
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
