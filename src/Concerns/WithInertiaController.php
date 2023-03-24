<?php

namespace Orchestra\Testbench\Concerns;

use Illuminate\Support\Facades\View;
use Inertia\ServiceProvider;

trait WithInertiaController
{

    public array $fileFinderExtensions = ['vue', 'js', 'jsx', 'ts', 'tsx', 'html', 'php'];
    public array $pathToPages = ['resources/js/Pages', 'resources/js/pages', 'resources/ts/Pages', 'resources/ts/pages', 'resources/app/Pages', 'resources/app/pages', 'resources/views/pages',];
    public string $viewPath = 'resources/views';


    public function setUp()
    {
        parent::setUp();
        View::addLocation($this->viewPath);
        $this->app->bind('inertia.testing.view-finder', function ($app) {
            $viewFinder = new \Illuminate\View\FileViewFinder(
                $app['files'],
                array_merge($app['config']->get('inertia.testing.page_paths'), $this->pathToPages),
                array_merge($app['config']->get('inertia.testing.page_extensions'), $this->fileFinderExtensions)
            );
            $viewFinder->addNamespace('WD', 'UI/resources/app/Pages');

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
