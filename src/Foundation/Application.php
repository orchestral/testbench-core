<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Queue\Queue;
use Illuminate\Support\Arr;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Workbench\Workbench;

/**
 * @api
 *
 * @phpstan-import-type TExtraConfig from \Orchestra\Testbench\Foundation\Config
 * @phpstan-import-type TOptionalExtraConfig from \Orchestra\Testbench\Foundation\Config
 *
 * @phpstan-type TConfig array{
 *   extra?: TOptionalExtraConfig,
 *   load_environment_variables?: bool,
 *   enabled_package_discoveries?: bool
 * }
 */
class Application
{
    use CreatesApplication {
        resolveApplicationResolvingCallback as protected resolveApplicationResolvingCallbackFromTrait;
        resolveApplicationConfiguration as protected resolveApplicationConfigurationFromTrait;
    }

    /**
     * The application base path.
     *
     * @var string|null
     */
    protected $basePath;

    /**
     * List of configurations.
     *
     * @var array<string, mixed>
     *
     * @phpstan-var TExtraConfig
     */
    protected $config = [
        'env' => [],
        'providers' => [],
        'dont-discover' => [],
        'bootstrappers' => [],
    ];

    /**
     * The application resolving callback.
     *
     * @var (callable(\Illuminate\Foundation\Application):(void))|null
     */
    protected $resolvingCallback;

    /**
     * Load Environment variables.
     *
     * @var bool
     */
    protected $loadEnvironmentVariables = false;

    /**
     * Create new application resolver.
     *
     * @param  string|null  $basePath
     * @param  (callable(\Illuminate\Foundation\Application):(void))|null  $resolvingCallback
     */
    public function __construct(?string $basePath = null, ?callable $resolvingCallback = null)
    {
        $this->basePath = $basePath;
        $this->resolvingCallback = $resolvingCallback;
    }

    /**
     * Create symlink to vendor path via new application instance.
     *
     * @param  string|null  $basePath
     * @param  string  $workingVendorPath
     * @return \Illuminate\Foundation\Application
     *
     * @codeCoverageIgnore
     */
    public static function createVendorSymlink(?string $basePath, string $workingVendorPath)
    {
        $app = static::create($basePath, null, ['extra' => ['dont-discover' => ['*']]]);

        (new Bootstrap\CreateVendorSymlink($workingVendorPath))->bootstrap($app);

        return $app;
    }

    /**
     * Create new application instance.
     *
     * @param  string|null  $basePath
     * @param  (callable(\Illuminate\Foundation\Application):(void))|null  $resolvingCallback
     * @param  array<string, mixed>  $options
     * @return \Illuminate\Foundation\Application
     *
     * @phpstan-param TConfig  $options
     */
    public static function create(?string $basePath = null, ?callable $resolvingCallback = null, array $options = [])
    {
        return (new static($basePath, $resolvingCallback))->configure($options)->createApplication();
    }

    /**
     * Flush the application states.
     *
     * @return void
     */
    public static function flushState(): void
    {
        Artisan::forgetBootstrappers();
        JsonResource::wrap('data');
        Queue::createPayloadUsing(null);
    }

    /**
     * Configure the application options.
     *
     * @param  array<string, mixed>  $options
     * @return $this
     *
     * @phpstan-param TConfig  $options
     */
    public function configure(array $options)
    {
        if (isset($options['load_environment_variables']) && \is_bool($options['load_environment_variables'])) {
            $this->loadEnvironmentVariables = $options['load_environment_variables'];
        }

        if (isset($options['enables_package_discoveries']) && \is_bool($options['enables_package_discoveries'])) {
            Arr::set($options, 'extra.dont-discover', []);
        }

        /** @var TExtraConfig $config */
        $config = Arr::only($options['extra'] ?? [], array_keys($this->config));

        $this->config = $config;

        return $this;
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
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return $this->config['providers'] ?? [];
    }

    /**
     * Get package bootstrapper.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageBootstrappers($app)
    {
        if (\is_null($bootstrappers = ($this->config['bootstrappers'] ?? null))) {
            return [];
        }

        return Arr::wrap($bootstrappers);
    }

    /**
     * Resolve application resolving callback.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    private function resolveApplicationResolvingCallback($app): void
    {
        $this->resolveApplicationResolvingCallbackFromTrait($app);

        if (\is_callable($this->resolvingCallback)) {
            \call_user_func($this->resolvingCallback, $app);
        }
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return $this->basePath ?? static::applicationBasePath();
    }

    /**
     * Resolve application core environment variables implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationEnvironmentVariables($app)
    {
        Env::disablePutenv();

        $app->terminating(static function () {
            Env::enablePutenv();
        });

        if ($this->loadEnvironmentVariables === true) {
            $app->make(LoadEnvironmentVariables::class)->bootstrap($app);
        }

        (new Bootstrap\LoadEnvironmentVariablesFromArray($this->config['env'] ?? []))->bootstrap($app);
    }

    /**
     * Resolve application core configuration implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConfiguration($app)
    {
        $this->resolveApplicationConfigurationFromTrait($app);

        (new Bootstrap\EnsuresDefaultConfiguration())->bootstrap($app);
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $kernel = Workbench::applicationConsoleKernel() ?? 'Orchestra\Testbench\Console\Kernel';

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
        $kernel = Workbench::applicationHttpKernel() ?? 'Orchestra\Testbench\Http\Kernel';

        if (file_exists($app->basePath('app/Http/Kernel.php')) && class_exists('App\Http\Kernel')) {
            $kernel = 'App\Http\Kernel';
        }

        $app->singleton('Illuminate\Contracts\Http\Kernel', $kernel);
    }
}
