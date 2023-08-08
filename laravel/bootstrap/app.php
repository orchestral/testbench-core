<?php

use Illuminate\Support\Env;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Bootstrap\StartWorkbench;

/**
 * Create Laravel application.
 *
 * @param  string  $workingPath
 * @return \Illuminate\Foundation\Application
 */
$createApp = function (string $workingPath) {
    if (! defined('TESTBENCH_WORKING_PATH') && ! is_null(Env::get('TESTBENCH_WORKING_PATH'))) {
        define('TESTBENCH_WORKING_PATH', Env::get('TESTBENCH_WORKING_PATH'));
    }

    $config = Config::loadFromYaml(
        defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : $workingPath
    );

    $hasEnvironmentFile = file_exists("{$workingPath}/.env");

    return Application::create(
        basePath: $config['laravel'],
        options: ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
        resolvingCallback: function ($app) use ($config) {
            (new StartWorkbench($config))->bootstrap($app);
        },
    );
};

$app = $createApp(realpath(__DIR__.'/../'));

unset($createApp);

/** @var \Illuminate\Routing\Router $router */
$router = $app->make('router');

collect(glob(__DIR__.'/../routes/testbench-*.php'))
    ->each(function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
