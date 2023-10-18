<?php

namespace Orchestra\Testbench\Foundation\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Orchestra\Testbench\Contracts\Config;

use function Orchestra\Testbench\after_resolving;
use function Orchestra\Testbench\workbench_path;

/**
 * @internal
 *
 * @phpstan-import-type TWorkbenchDiscoversConfig from \Orchestra\Testbench\Foundation\Config
 */
final class DiscoverRoutes
{
    /**
     * The project configuration.
     *
     * @var \Orchestra\Testbench\Contracts\Config
     */
    public $config;

    /**
     * Construct a new Create Vendor Symlink bootstrapper.
     *
     * @param  \Orchestra\Testbench\Contracts\Config  $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Bootstrap the given application.
     */
    public function bootstrap(Application $app): void
    {
        /** @var TWorkbenchDiscoversConfig $config */
        $config = $this->config->getWorkbenchDiscoversAttributes();

        tap($app->make('router'), static function (Router $router) use ($config) {
            foreach (['web', 'api'] as $group) {
                if (($config[$group] ?? false) === true) {
                    if (file_exists($route = workbench_path("routes/{$group}.php"))) {
                        $router->middleware($group)->group($route);
                    }
                }
            }
        });

        if ($app->runningInConsole() && ($config['commands'] ?? false) === true) {
            if (file_exists($console = workbench_path('routes/console.php'))) {
                require $console;
            }
        }

        after_resolving($app, 'translator', static function ($translator) {
            /** @var \Illuminate\Contracts\Translation\Loader $translator */
            $translator->addNamespace(
                'workbench',
                is_dir(workbench_path('/lang')) ? workbench_path('/lang') : workbench_path('/resources/lang')
            );
        });

        after_resolving($app, 'view', static function ($view) use ($config) {
            /** @var \Illuminate\Contracts\View\Factory|\Illuminate\View\Factory $view */
            $path = workbench_path('/resources/views');

            if (($config['views'] ?? false) === true && method_exists($view, 'addLocation')) {
                $view->addLocation($path);
            } else {
                $view->addNamespace('workbench', $path);
            }
        });

        after_resolving($app, 'blade.compiler', static function ($blade) use ($config) {
            /** @var \Illuminate\View\Compilers\BladeCompiler $blade */
            if (($config['views'] ?? false) === false) {
                $blade->componentNamespace('Workbench\\App\\View\\Components', 'workbench');
            }
        });
    }
}
