<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use Inertia\ServiceProvider;

/**
 * @property class-string $inertiaMiddleware
 */
trait WithInertia
{
    /**
     * Inertia has been resolved.
     * 
     * @var bool
     */
    protected $inertiaWasResolved = false;

    /**
     * Setup the test environment.
     */
    protected function setupWithInertia(): void
    {
        $this->app->register(ServiceProvider::class);

        if (property_exists($this, 'inertiaMiddleware')) {
            $this->app['router']->middleware($this->inertiaMiddleware);
        }

        $this->app['config']->set('inertia.testing.ensure_pages_exist', false);
    }

    /**
     * Define Inertia for the test environment.
     *
     * @param  (callable(\Illuminate\View\FileViewFinder, \Illuminate\Foundation\Application, \Illuminate\Contracts\Config\Repository):void)|null  $callback
     * @return void
     */
    protected function defineInertia(?callable $callback = null): void
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = $this->app;

        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app['config'];

        if ($this->inertiaWasResolved === false) {
            $this->resolveInertiaFileViewFinder($this->app);
        }

        /** @var \Illuminate\View\FileViewFinder $finder */
        $finder = $app['inertia.testing.view-finder'];
        
        if (is_callable($callback)) {
            value($callback, $finder, $app, $config);
        }

        $this->inertiaWasResolved = true;
    }

    /**
     * Configure Inertia's File View Finder.
     *
     * @param  string  $view
     * @param  array<int, string>  $paths
     * @return void
     */
    protected function defineInertiaPages(?string $view, array $paths = [])
    {
        if (! is_null($location)) {
            $this->app['view']->addLocation($view);
        }

        $this->defineInertia(function (FileViewFinder $finder, $app, $config) use ($paths) {
            $config->set('inertia.testing.ensure_pages_exist', true);

            $finder->setPaths(array_merge($finder->getPaths(), $paths));
        });
    }

    /**
     * Resolve Inertia's File View Finder instance.
     * 
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveInertiaFileViewFinder($app) 
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $app['config'];

        $extensions = ['vue', 'js', 'jsx', 'ts', 'tsx', 'html', 'php'];

        $finder = new FileViewFinder(
            new Filesystem(),
            $config->get('inertia.testing.page_paths'),
            array_merge($config->get('inertia.testing.page_extensions'), $extensions)
        );

        $this->app->instance('inertia.testing.view-finder', $finder);
    }
}
