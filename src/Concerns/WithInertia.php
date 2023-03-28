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
     * @return void
     */
    protected function defineInertiaPages(?string $view, array $paths = [])
    {
        if (! is_null($location)) {
            $this->app['view']->addLocation($view);
        }

        $resolver = function (FileViewFinder $finder) use ($paths) {
            $finder->setPaths(array_merge($finder->getPaths(), $paths));
        };

        if ($this->inertiaDefined === false) {
            $this->defineInertia($resolver);
        } else {
            value($resolver, $this->app['inertia.testing.view-finder'], $this->app, $this->app['config']);
        }
    }
}
