<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Attributes\RequiresEnv;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Attributes\WithImmutableDates;
use Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\Features\TestingFeature;
use Orchestra\Testbench\Foundation\PackageManifest;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use function Illuminate\Filesystem\join_paths;
use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\default_skeleton_path;
use function Orchestra\Testbench\refresh_router_lookups;

/**
 * @property bool|null $enablesPackageDiscoveries
 * @property bool|null $loadEnvironmentVariables
 */
trait CreatesApplication
{
    use InteractsWithWorkbench;
    use WithLaravelBootstrapFile;

    /**
     * Get Application's base path.
     *
     * @api
     *
     * @return string
     */
    public static function applicationBasePath()
    {
        return static::applicationBasePathUsingWorkbench() ?? default_skeleton_path();
    }

    /**
     * Ignore package discovery from.
     *
     * @api
     *
     * @return array<int, string>
     */
    public function ignorePackageDiscoveriesFrom()
    {
        return $this->ignorePackageDiscoveriesFromUsingWorkbench() ?? ['*'];
    }

    /**
     * Get application timezone.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return string|null
     */
    protected function getApplicationTimezone($app)
    {
        return $app['config']['app.timezone'];
    }

    /**
     * Override application bindings.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string|class-string, string|class-string>
     */
    protected function overrideApplicationBindings($app)
    {
        return [];
    }

    /**
     * Resolve application bindings.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    final protected function resolveApplicationBindings($app): void
    {
        foreach ($this->overrideApplicationBindings($app) as $original => $replacement) {
            $app->bind($original, $replacement);
        }
    }

    /**
     * Get application aliases.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function getApplicationAliases($app)
    {
        return $app['config']['app.aliases'];
    }

    /**
     * Override application aliases.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function overrideApplicationAliases($app)
    {
        return [];
    }

    /**
     * Resolve application aliases.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    final protected function resolveApplicationAliases($app): array
    {
        $aliases = new Collection($this->getApplicationAliases($app));
        $overrides = $this->overrideApplicationAliases($app);

        if (! empty($overrides)) {
            $aliases->transform(static fn ($alias, $name) => $overrides[$name] ?? $alias);
        }

        return $aliases->merge($this->getPackageAliases($app))->all();
    }

    /**
     * Get package aliases.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app)
    {
        return [];
    }

    /**
     * Get package bootstrapper.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageBootstrappers($app)
    {
        return $this->getPackageBootstrappersUsingWorkbench($app) ?? [];
    }

    /**
     * Get application providers.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getApplicationProviders($app)
    {
        return $app['config']['app.providers'];
    }

    /**
     * Override application aliases.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<class-string, class-string>
     */
    protected function overrideApplicationProviders($app)
    {
        return [];
    }

    /**
     * Resolve application aliases.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    final protected function resolveApplicationProviders($app): array
    {
        $providers = new Collection($this->getApplicationProviders($app));
        $overrides = $this->overrideApplicationProviders($app);

        if (! empty($overrides)) {
            $providers->transform(static fn ($provider) => $overrides[$provider] ?? $provider);
        }

        return $providers->merge($this->getPackageProviders($app))->all();
    }

    /**
     * Get package providers.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return $this->getPackageProvidersUsingWorkbench($app) ?? [];
    }

    /**
     * Get base path.
     *
     * @internal
     *
     * @return string
     */
    protected function getBasePath()
    {
        return static::applicationBasePath();
    }

    /**
     * Get the default application bootstrap file path (if exists).
     *
     * @internal
     *
     * @param  string  $filename
     * @return string|false
     */
    protected function getDefaultApplicationBootstrapFile(string $filename): string|false
    {
        return realpath(default_skeleton_path(join_paths('bootstrap', $filename)));
    }

    /**
     * Creates the application.
     *
     * @internal
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = $this->resolveApplication();

        $this->resolveApplicationResolvingCallback($app);

        $this->resolveApplicationBindings($app);
        $this->resolveApplicationExceptionHandler($app);
        $this->resolveApplicationCore($app);
        $this->resolveApplicationEnvironmentVariables($app);
        $this->resolveApplicationConfiguration($app);
        $this->resolveApplicationHttpKernel($app);
        $this->resolveApplicationHttpMiddlewares($app);
        $this->resolveApplicationConsoleKernel($app);
        $this->resolveApplicationBootstrappers($app);
        $this->refreshApplicationRouteNameLookups($app);

        return $app;
    }

    /**
     * Create the default application implementation.
     *
     * @internal
     *
     * @return \Illuminate\Foundation\Application
     */
    final protected function resolveDefaultApplication()
    {
        return (new ApplicationBuilder(new Application($this->getBasePath())))
            ->withProviders()
            ->withMiddleware(static function ($middleware) {
                //
            })
            ->withCommands()
            ->create();
    }

    /**
     * Resolve application implementation.
     *
     * @api
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        static::$cacheApplicationBootstrapFile ??= $this->getApplicationBootstrapFile('app.php');

        if (\is_string(static::$cacheApplicationBootstrapFile)) {
            $APP_BASE_PATH = $this->getBasePath();

            return require static::$cacheApplicationBootstrapFile;
        }

        return $this->resolveDefaultApplication();
    }

    /**
     * Resolve application resolving callback.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    private function resolveApplicationResolvingCallback($app): void
    {
        $app->bind(
            'Illuminate\Foundation\Bootstrap\LoadConfiguration',
            static::usesTestingConcern() && ! static::usesTestingConcern(WithWorkbench::class)
                ? 'Orchestra\Testbench\Bootstrap\LoadConfiguration'
                : 'Orchestra\Testbench\Bootstrap\LoadConfigurationWithWorkbench'
        );

        PackageManifest::swap($app, $this);
    }

    /**
     * Resolve application core environment variables implementation.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationEnvironmentVariables($app)
    {
        if (property_exists($this, 'loadEnvironmentVariables') && $this->loadEnvironmentVariables === true) {
            $app->make(LoadEnvironmentVariables::class)->bootstrap($app);
        }

        $attributeCallbacks = TestingFeature::run(
            testCase: $this,
            attribute: fn () => $this->parseTestMethodAttributes($app, WithEnv::class), // @phpstan-ignore-line
        )->get('attribute');

        TestingFeature::run(
            testCase: $this,
            attribute: fn () => $this->parseTestMethodAttributes($app, RequiresEnv::class), // @phpstan-ignore-line
        );

        if ($this instanceof PHPUnitTestCase && method_exists($this, 'beforeApplicationDestroyed')) {
            $this->beforeApplicationDestroyed(function () use ($attributeCallbacks) {
                $attributeCallbacks->handle();
            });
        }
    }

    /**
     * Resolve application core configuration implementation.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConfiguration($app)
    {
        $app->make('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($app);
        $app->make('Orchestra\Testbench\Bootstrap\ConfigureRay')->bootstrap($app);
        $app->make('Orchestra\Testbench\Foundation\Bootstrap\SyncDatabaseEnvironmentVariables')->bootstrap($app);

        tap($this->getApplicationTimezone($app), static function ($timezone) {
            ! \is_null($timezone) && date_default_timezone_set($timezone);
        });

        tap($app['config'], function ($config) use ($app) {
            if (! $app->bound('env')) {
                $app->detectEnvironment(static fn () => $config->get('app.env', 'workbench'));
            }

            $config->set([
                'app.aliases' => $this->resolveApplicationAliases($app),
                'app.providers' => $this->resolveApplicationProviders($app),
            ]);

            if (\is_string($bootstrapProviderPath = $this->getApplicationBootstrapFile('providers.php'))) {
                RegisterProviders::merge([], $bootstrapProviderPath);
            }

            TestingFeature::run(
                testCase: $this,
                attribute: fn () => $this->parseTestMethodAttributes($app, WithConfig::class), // @phpstan-ignore-line
            );
        });
    }

    /**
     * Resolve application core implementation.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationCore($app)
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        if ($this->isRunningTestCase()) {
            $app->detectEnvironment(static fn () => 'testing');
        }
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(ConsoleKernelContract::class, $this->applicationConsoleKernelUsingWorkbench($app));
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(HttpKernelContract::class, $this->applicationHttpKernelUsingWorkbench($app));
    }

    /**
     * Resolve application HTTP default middlewares.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpMiddlewares($app)
    {
        after_resolving($app, HttpKernelContract::class, function ($kernel, $app) {
            /** @var \Illuminate\Foundation\Http\Kernel $kernel */
            $middleware = new Middleware();

            $kernel->setGlobalMiddleware($middleware->getGlobalMiddleware());
            $kernel->setMiddlewareGroups($middleware->getMiddlewareGroups());
            $kernel->setMiddlewareAliases($middleware->getMiddlewareAliases());
        });
    }

    /**
     * Resolve application HTTP exception handler.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationExceptionHandler($app)
    {
        $app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', $this->applicationExceptionHandlerUsingWorkbench($app));
    }

    /**
     * Resolve application bootstrapper.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationBootstrappers($app)
    {
        $app->make(
            $this->isRunningTestCase()
                ? 'Orchestra\Testbench\Bootstrap\HandleExceptions'
                : 'Illuminate\Foundation\Bootstrap\HandleExceptions'
        )->bootstrap($app);

        $app->make('Illuminate\Foundation\Bootstrap\RegisterFacades')->bootstrap($app);
        $app->make('Illuminate\Foundation\Bootstrap\SetRequestForConsole')->bootstrap($app);
        $app->make('Illuminate\Foundation\Bootstrap\RegisterProviders')->bootstrap($app);

        if (class_exists('Illuminate\Database\Eloquent\LegacyFactoryServiceProvider')) {
            $app->register('Illuminate\Database\Eloquent\LegacyFactoryServiceProvider');
        }

        TestingFeature::run(
            testCase: $this,
            default: function () use ($app) {
                $this->defineEnvironment($app);
                $this->getEnvironmentSetUp($app);
            },
            annotation: function () use ($app) {
                $this->parseTestMethodAnnotations($app, 'environment-setup'); // @phpstan-ignore-line
                $this->parseTestMethodAnnotations($app, 'define-env'); // @phpstan-ignore-line
            },
            attribute: function () use ($app) {
                $this->parseTestMethodAttributes($app, WithImmutableDates::class); // @phpstan-ignore-line
                $this->parseTestMethodAttributes($app, DefineEnvironment::class); // @phpstan-ignore-line
            },
            pest: fn () => $this->defineEnvironmentUsingPest($app), // @phpstan-ignore-line
        );

        $this->resolveApplicationRateLimiting($app);

        if (static::usesTestingConcern(WithWorkbench::class)) {
            $this->bootDiscoverRoutesForWorkbench($app); // @phpstan-ignore-line
        }

        if ($this->isRunningTestCase() && static::usesTestingConcern(HandlesRoutes::class)) {
            $this->setUpApplicationRoutes($app); // @phpstan-ignore-line
        }

        $app->make('Illuminate\Foundation\Bootstrap\BootProviders')->bootstrap($app);

        foreach ($this->getPackageBootstrappers($app) as $bootstrap) {
            $app->make($bootstrap)->bootstrap($app);
        }

        $app->make(ConsoleKernelContract::class)->bootstrap();
    }

    /**
     * Refresh route name lookup for the application.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    final protected function refreshApplicationRouteNameLookups($app): void
    {
        /** @var \Illuminate\Routing\Router $router */
        $router = $app->make('router');

        refresh_router_lookups($router);

        after_resolving($app, 'url', static function ($url, $app) use ($router) {
            refresh_router_lookups($router);
        });
    }

    /**
     * Resolve application rate limiting configuration.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationRateLimiting($app)
    {
        RateLimiter::for(
            'api', static fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
        );
    }

    /**
     * Reset artisan commands for the application.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    final protected function resetApplicationArtisanCommands($app): void
    {
        $app[ConsoleKernelContract::class]->setArtisan(null);
    }

    /**
     * Define environment setup.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Define environment.
    }

    /**
     * Define environment setup.
     *
     * @api
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Define your environment setup.
    }
}
