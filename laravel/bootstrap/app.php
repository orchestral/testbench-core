<?php

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use function Orchestra\Testbench\default_environment_variables;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Bootstrap\LoadEnvironmentVariablesFromArray;
use Orchestra\Testbench\Foundation\Config;

/**
 * Create Laravel application.
 *
 * @param  string  $workingPath
 * @return \Illuminate\Foundation\Application
 */
$createApp = function (string $workingPath) {
    $config = Config::loadFromYaml($workingPath);

    $hasEnvironmentFile = file_exists("{$workingPath}/.env");

    return Application::create(
        $config['laravel'],
        function ($app) use ($config, $hasEnvironmentFile) {
            $app->instance(ConfigContract::class, $config);

            if ($hasEnvironmentFile === false) {
                (new LoadEnvironmentVariablesFromArray(
                    ! empty($config['env']) ? $config['env'] : default_environment_variables()
                ))->bootstrap($app);
            }
        },
        ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
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
