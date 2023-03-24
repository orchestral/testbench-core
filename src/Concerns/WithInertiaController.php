<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Facades\View;
use Inertia\ServiceProvider;

trait WithInertiaController
{
    /**
     * @param array $pageNameSpaces e.g. ['admin' => 'resources/views/admin'] if you have a namespace in your Inertia pages separated by ::
     * @param string $viewPath 
     * @param array $pathToPages
     * @param array $fileFinderExtensions
     */
    public function loadInertia(
        array $pageNameSpaces = [],
        string $viewPath = 'resources/views',
        array $pathToPages = ['resources/js/Pages', 'resources/js/pages', 'resources/ts/Pages', 'resources/ts/pages', 'resources/app/Pages', 'resources/app/pages', 'resources/views/pages',],
        array $fileFinderExtensions = ['vue', 'js', 'jsx', 'ts', 'tsx', 'html', 'php']
    ) {
        View::addLocation($viewPath);
        $this->app->bind('inertia.testing.view-finder', function ($app) use ($pathToPages, $fileFinderExtensions, $pageNameSpaces) {
            $viewFinder = new \Illuminate\View\FileViewFinder(
                $app['files'],
                array_merge($app['config']->get('inertia.testing.page_paths'), $pathToPages),
                array_merge($app['config']->get('inertia.testing.page_extensions'), $fileFinderExtensions)
            );
            foreach ($pageNameSpaces as $namespace => $namespacePagePath) {
                $viewFinder->addNamespace($namespace, $namespacePagePath);
            }
            return $viewFinder;
        });
    }

    protected function getPackageProviders($app)
    {
        return array_merge([
            ServiceProvider::class,
        ], parent::getPackageProviders($app));
    }
}
