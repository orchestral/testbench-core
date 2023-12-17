<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\RateLimiter;
use Orchestra\Testbench\Attributes\DefineEnvironment;
use Orchestra\Testbench\Attributes\RequiresEnv;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\Foundation\PackageManifest;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * @api
 *
 * @property bool|null $enablesPackageDiscoveries
 * @property bool|null $loadEnvironmentVariables
 */
trait CreatesApplication
{
    use HandlesTestingFeature;
    use InteractsWithWorkbench;

    /**
     * Get Application's base path.
     *
     * @return string
     */
    public static function applicationBasePath()
    {
        return static::applicationBasePathUsingWorkbench() ?? (string) realpath(__DIR__.'/../../laravel');
    }

    /**
     * Ignore package discovery from.
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
            $aliases->transform(static function ($alias, $name) use ($overrides) {
                return $overrides[$name] ?? $alias;
            });
        }

        return $aliases->merge($this->getPackageAliases($app))->all();
    }

    /**
     * Get package aliases.
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
            $providers->transform(static function ($provider) use ($overrides) {
                return $overrides[$provider] ?? $provider;
            });
        }

        return $providers->merge($this->getPackageProviders($app))->all();
    }

    /**
     * Get package providers.
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
     * @return string
     */
    protected function getBasePath()
    {
        return static::applicationBasePath();
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = $this->resolveApplication();

        $this->resolveApplicationBindings($app);
        $this->resolveApplicationExceptionHandler($app);
        $this->resolveApplicationCore($app);
        $this->resolveApplicationEnvironmentVariables($app);
        $this->resolveApplicationConfiguration($app);
        $this->resolveApplicationHttpKernel($app);
        $this->resolveApplicationConsoleKernel($app);
        $this->resolveApplicationBootstrappers($app);

        return $app;
    }

    /**
     * Create the default application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    final protected function resolveDefaultApplication()
    {
        return new Application($this->getBasePath());
    }

    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        return tap($this->resolveDefaultApplication(), function ($app) {
            $app->bind(
                'Illuminate\Foundation\Bootstrap\LoadConfiguration',
                static::usesTestingConcern() && ! static::usesTestingConcern(WithWorkbench::class)
                    ? 'Orchestra\Testbench\Bootstrap\LoadConfiguration'
                    : 'Orchestra\Testbench\Bootstrap\LoadConfigurationWithWorkbench'
            );

            PackageManifest::swap($app, $this);
        });
    }

    /**
     * Resolve application core environment variables implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationEnvironmentVariables($app)
    {
        if (property_exists($this, 'loadEnvironmentVariables') && $this->loadEnvironmentVariables === true) {
            $app->make(LoadEnvironmentVariables::class)->bootstrap($app);
        }

        $attributeCallbacks = $this->resolveTestbenchTestingFeature(
            attribute: fn () => $this->parseTestMethodAttributes($app, WithEnv::class), // @phpstan-ignore-line
        )->get('attribute');

        $this->resolveTestbenchTestingFeature(
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
                $app->detectEnvironment(static function () use ($config) {
                    return $config->get('app.env', 'workbench');
                });
            }

            $config->set([
                'app.aliases' => $this->resolveApplicationAliases($app),
                'app.providers' => $this->resolveApplicationProviders($app),
            ]);

            $this->resolveTestbenchTestingFeature(
                attribute: fn () => $this->parseTestMethodAttributes($app, WithConfig::class), // @phpstan-ignore-line
            );
        });
    }

    /**
     * Resolve application core implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationCore($app)
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        if ($this->isRunningTestCase()) {
            $app->detectEnvironment(static function () {
                return 'testing';
            });
        }
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', $this->applicationConsoleKernelUsingWorkbench($app));
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', $this->applicationHttpKernelUsingWorkbench($app));
    }

    /**
     * Resolve application HTTP exception handler.
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
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationBootstrappers($app)
    {
        if ($this instanceof PHPUnitTestCase) {
            $app->make('Orchestra\Testbench\Bootstrap\HandleExceptions', ['testbench' => $this])->bootstrap($app);
        } else {
            $app->make('Illuminate\Foundation\Bootstrap\HandleExceptions')->bootstrap($app);
        }

        $app->make('Illuminate\Foundation\Bootstrap\RegisterFacades')->bootstrap($app);
        $app->make('Illuminate\Foundation\Bootstrap\SetRequestForConsole')->bootstrap($app);
        $app->make('Illuminate\Foundation\Bootstrap\RegisterProviders')->bootstrap($app);

        if (class_exists('Illuminate\Database\Eloquent\LegacyFactoryServiceProvider')) {
            $app->register('Illuminate\Database\Eloquent\LegacyFactoryServiceProvider');
        }

        $this->resolveTestbenchTestingFeature(
            default: function () use ($app) {
                $this->defineEnvironment($app);
                $this->getEnvironmentSetUp($app);
            },
            annotation: function () use ($app) {
                $this->parseTestMethodAnnotations($app, 'environment-setup'); // @phpstan-ignore-line
                $this->parseTestMethodAnnotations($app, 'define-env'); // @phpstan-ignore-line
            },
            attribute: fn () => $this->parseTestMethodAttributes($app, DefineEnvironment::class), // @phpstan-ignore-line
        );

        $this->resolveApplicationRateLimiting($app);

        if (static::usesTestingConcern(WithWorkbench::class)) {
            /** @phpstan-ignore-next-line */
            $this->bootDiscoverRoutesForWorkbench($app);
        }

        $app->make('Illuminate\Foundation\Bootstrap\BootProviders')->bootstrap($app);

        if ($this->isRunningTestCase() && static::usesTestingConcern(HandlesRoutes::class)) {
            /** @phpstan-ignore-next-line */
            $this->setUpApplicationRoutes($app);
        }

        foreach ($this->getPackageBootstrappers($app) as $bootstrap) {
            $app->make($bootstrap)->bootstrap($app);
        }

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $this->refreshApplicationRouteNameLookups($app);
    }

    /**
     * Refresh route name lookup for the application.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    final protected function refreshApplicationRouteNameLookups($app)
    {
        $refreshNameLookups = static function ($app) {
            $app['router']->getRoutes()->refreshNameLookups();
        };

        $refreshNameLookups($app);

        $app->resolving('url', fn () => $refreshNameLookups($app));
    }

    /**
     * Resolve application rate limiting configuration.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationRateLimiting($app)
    {
        RateLimiter::for('api', static function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Reset artisan commands for the application.
     *
     * @internal
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    final protected function resetApplicationArtisanCommands($app)
    {
        $app['Illuminate\Contracts\Console\Kernel']->setArtisan(null);
    }

    /**
     * Define environment setup.
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
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Define your environment setup.
    }
}
