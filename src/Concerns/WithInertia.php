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
     * Inertia has been defined.
     * 
     * @var bool
     */
    protected $inertiaDefined = false;

    /**
     * Setup the test environment.
     */
    protected function setupWithInertia(): void
    {
        $this->app->register(ServiceProvider::class);

        if (property_exists($this, 'inertiaMiddleware')) {
            $this->app['router']->middleware($this->inertiaMiddleware);
        }
    }

    /**
     * Define Inertia for the test environment.
     *
     * @param  string  $location
     * @param  (callable(\Illuminate\View\FileViewFinder, \Illuminate\Foundation\Application, \Illuminate\Contracts\Config\Repository):void)|null  $callback
     * @param  bool|null  $ensurePageExists
     * @return void
     */
    protected function defineInertia(callable $callback = null): void
    {
        /** @var \Illuminate\Contracts\Config\Repository $config */
        $config = $this->app['config'];

        $config->set('inertia.testing.ensure_pages_exist', false);

        $pageExtensions = ['vue', 'js', 'jsx', 'ts', 'tsx', 'html', 'php'];

        $finder = new FileViewFinder(
            new Filesystem(),
            $config->get('inertia.testing.page_paths'),
            array_merge($config->get('inertia.testing.page_extensions'), $pageExtensions)
        );

        if (is_callable($callback)) {
            value($callback, $finder, $this->app, $config);
        }

        $this->instance('inertia.testing.view-finder', $finder);

        $this->inertiaDefined = true;
    }

    /**
     * Configure Inertia's File View Finder.
     *
     * @param  string  $view
     * @param  array<int, string>  $paths
     * @param  bool  $ensureExists
     * @return void
     */
    protected function configureInertiaPages(?string $view, array $paths = [], $ensureExists = true)
    {
        if ($this->inertiaDefined === false) {
            $this->defineInertia();
        }

        $finder = $this->app['inertia.testing.view-finder'];

        $this->app['config']->set('inertia.testing.ensure_pages_exist', $ensureExists);

        if (! is_null($location)) {
            $this->app['view']->addLocation($view);
        }

        $finder->setPaths(array_merge($finder->getPaths(), $paths));
    }
}

