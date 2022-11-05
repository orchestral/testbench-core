<?php

use Orchestra\Testbench\Console\Commander;
use function Orchestra\Testbench\default_environment_variables;

/**
 * Create Laravel application.
 *
 * @return \Illuminate\Foundation\Application
 */
$createApp = function () {
    $config = ['env' => default_environment_variables(), 'providers' => []];

    return (new Commander($config, getcwd()))->laravel();
};

$app = $createApp();

unset($createApp);

/** @var \Illuminate\Routing\Router $router */
$router = $app->make('router');

collect(glob(__DIR__.'/../routes/testbench-*.php'))
    ->each(function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
