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

        after_resolving($app, 'view', static function ($view) {
            /** @var \Illuminate\Contracts\View\Factory $view */
            $view->addNamespace('workbench', workbench_path('/resources/views'));
        });

        after_resolving($app, 'blade.compiler', static function ($blade) {
            /** @var \Illuminate\View\Compilers\BladeCompiler $blade */
            $blade->componentNamespace('Workbench\\App\\View\\Components', 'workbench');
            $blade->anonymousComponentNamespace(workbench_path('/resources/views/components'), 'workbench');
        });
    }
}
