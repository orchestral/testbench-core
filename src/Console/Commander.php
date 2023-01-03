<?php

namespace Orchestra\Testbench\Console;

use Illuminate\Console\Concerns\InteractsWithSignals;
use Illuminate\Console\Signals;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Console\Concerns\CopyTestbenchFiles;
use Orchestra\Testbench\Foundation\TestbenchServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SignalRegistry\SignalRegistry;
use Throwable;

/**
 * @internal
 *
 * @phpstan-type TConfig array{laravel: string|null, env: array|null, providers: array|null, dont-discover: array|null}
 */
class Commander
{
    use CopyTestbenchFiles,
        InteractsWithSignals;

    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application|null
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
     * The environment file name.
     *
     * @var string
     */
    protected $environmentFile = '.env';

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
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $input = new ArgvInput();
        $output = new ConsoleOutput();

        try {
            $laravel = $this->laravel();
            $kernel = $laravel->make(ConsoleKernel::class);

            $this->prepareCommandSignals();

            $status = $kernel->handle($input, $output);

            $kernel->terminate($input, $status);
        } catch (Throwable $error) {
            $status = $this->handleException($output, $error);
        } finally {
            $this->handleTerminatingConsole();
        }

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

            tap(Application::createVendorSymlink($laravelBasePath, $this->workingPath.'/vendor'), function ($app) use ($laravelBasePath) {
                $filesystem = new Filesystem();

                $this->copyTestbenchConfigurationFile($app, $filesystem, $this->workingPath);

                if (! file_exists("{$laravelBasePath}/.env")) {
                    $this->copyTestbenchDotEnvFile($app, $filesystem, $this->workingPath);
                }
            });

            $hasEnvironmentFile = file_exists("{$laravelBasePath}/.env");

            $options = array_filter([
                'load_environment_variables' => $hasEnvironmentFile,
                'extra' => [
                    'providers' => Arr::get($this->config, 'providers', []),
                    'dont-discover' => Arr::get($this->config, 'dont-discover', []),
                    'env' => Arr::get($this->config, 'env', []),
                ],
            ]);

            $this->app = Application::create(
                basePath: $this->getBasePath(),
                resolvingCallback: $this->resolveApplicationCallback(),
                options: $options
            );
        }

        return $this->app;
    }

    /**
     * Resolve application implementation.
     *
     * @return \Closure(\Illuminate\Foundation\Application): void
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
     * Get Application base path.
     *
     * @return string
     */
    public static function applicationBasePath()
    {
        return Application::applicationBasePath();
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
        tap($this->laravel()->make(ExceptionHandler::class), static function ($handler) use ($error, $output) {
            $handler->report($error);
            $handler->renderForConsole($output, $error);
        });

        return 1;
    }

    /**
     * Prepare command signals.
     *
     * @return void
     */
    protected function prepareCommandSignals(): void
    {
        Signals::resolveAvailabilityUsing(function () {
            return \extension_loaded('pcntl');
        });

        Signals::whenAvailable(function () {
            $this->signals ??= new Signals(new SignalRegistry());

            Collection::make(Arr::wrap([SIGINT]))
                ->each(
                    fn ($signal) => $this->signals->register($signal, fn () => $this->handleTerminatingConsole())
                );
        });
    }
}
