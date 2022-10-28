<?php

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

    if (empty($config['env'])) {
        $APP_KEY = $_SERVER['APP_KEY'] ?? $_ENV['APP_KEY'] ?? 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF';
        $DB_CONNECTION = $_SERVER['DB_CONNECTION'] ?? $_ENV['DB_CONNECTION'] ?? 'testing';

        $config['env'] = ['APP_KEY="'.$APP_KEY.'"', 'APP_DEBUG=(true)', 'DB_CONNECTION="'.$DB_CONNECTION.'"'];
    }

    $hasEnvironmentFile = file_exists("{$workingPath}/.env");

    return Application::create(
        basePath: $config['laravel'],
        resolvingCallback: function ($app) use ($config, $hasEnvironmentFile) {
            if ($hasEnvironmentFile === false) {
                (new LoadEnvironmentVariablesFromArray($config['env'] ?? []))->bootstrap($app);
            }
        },
        options: ['enables_package_discoveries' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
    );
};

$app = $createApp(realpath(__DIR__.'/../'));

/** @var \Illuminate\Routing\Router $router */
$router = $app->make('router');

unset($createApp);

collect(glob(__DIR__.'/../routes/testbench-*.php'))
    ->each(function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
