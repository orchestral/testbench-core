<?php

use Illuminate\Support\Env;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Bootstrap\LoadEnvironmentVariablesFromArray;
use Orchestra\Testbench\Foundation\Bootstrap\StartWorkbench;
use Orchestra\Testbench\Foundation\Config;

use function Orchestra\Testbench\default_environment_variables;

/**
 * Create Laravel application.
 *
 * @param  string  $workingPath
 * @return \Illuminate\Foundation\Application
 */
$createApp = function (string $workingPath) {
    $config = Config::loadFromYaml(
        defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : $workingPath
    );

    $hasEnvironmentFile = file_exists("{$workingPath}/.env");

    return Application::create(
        $config['laravel'],
        function ($app) use ($config, $hasEnvironmentFile) {
            (new StartWorkbench($config))->bootstrap($app);

            if ($hasEnvironmentFile === false) {
                (new LoadEnvironmentVariablesFromArray(
                    ! empty($config['env']) ? $config['env'] : default_environment_variables()
                ))->bootstrap($app);
            }
        },
        ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
    );
};

if (! defined('TESTBENCH_WORKING_PATH') && ! is_null(Env::get('TESTBENCH_WORKING_PATH'))) {
    define('TESTBENCH_WORKING_PATH', Env::get('TESTBENCH_WORKING_PATH'));
}

$app = $createApp(realpath(__DIR__.'/../'));

unset($createApp);

/** @var \Illuminate\Routing\Router $router */
$router = $app->make('router');

collect(glob(__DIR__.'/../routes/testbench-*.php'))
    ->each(function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
