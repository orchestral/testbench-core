<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;
use Inertia\ServiceProvider;

trait WithInertia
{
    /**
     * Inertia route middleware.
     *
     * @var class-string|null
     */
    protected $inertiaMiddleware;

    /**
     * Available Inertia page extensions.
     *
     * @var array<int, string>
     */
    protected $inertiaPageExtensions = ['vue', 'js', 'jsx', 'ts', 'tsx', 'html', 'php'];

    /**
     * Available Inertia page paths.
     *
     * @var array<int, string>
     */
    protected $inertiaPagePaths = [
        'resources/js/Pages',
        'resources/js/pages',
        'resources/ts/Pages',
        'resources/ts/pages',
        'resources/app/Pages',
        'resources/app/pages',
        'resources/views/pages',
    ];

    /**
     * Setup the test environment.
     */
    protected function setupWithInertia(): void
    {
        $this->app->register(ServiceProvider::class);

        if (! is_null($this->inertiaMiddleware)) {
            Route::middleware($this->inertiaMiddleware);
        }
    }

    /**
     * Configure Inertia for the test.
     *
     * @param  string  $location
     * @param  array<string, string>  $namespaces
     * @return void
     */
    protected function defineInertia(string $location = 'resources/views', array $namespaces = []): void
    {
        View::addLocation($location);

        $this->instance('inertia.testing.view-finder', function ($app) use ($namespaces) {
            /** @var \Illuminate\Foundation\Application $app */
            $finder = new FileViewFinder(
                $app['files'],
                array_merge($app['config']->get('inertia.testing.page_paths'), $this->inertiaPagePaths),
                array_merge($app['config']->get('inertia.testing.page_extensions'), $this->inertiaPageExtensions)
            );

            foreach ($namespaces as $namespace => $path) {
                $finder->addNamespace($namespace, $path);
            }

            return $finder;
        });
    }
}
