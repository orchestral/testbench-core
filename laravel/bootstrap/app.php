<?php

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Bootstrap\LoadEnvironmentVariablesFromArray;
use Orchestra\Testbench\Foundation\Config;

/**
 * Create Laravel application.
 *
 * @return \Illuminate\Foundation\Application
 */
$createApp = function () {
    $workingPath = realpath(__DIR__.'/../');

    $APP_KEY = $_SERVER['APP_KEY'] ?? $_ENV['APP_KEY'] ?? 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF';
    $DB_CONNECTION = $_SERVER['DB_CONNECTION'] ?? $_ENV['DB_CONNECTION'] ?? 'testing';

    $config = Config::loadFromYaml(
        workingPath: $workingPath,
        defaults: ['env' => ['APP_KEY="'.$APP_KEY.'"', 'DB_CONNECTION="'.$DB_CONNECTION.'"'], 'providers' => []]
    );

    $hasEnvironmentFile = file_exists("{$workingPath}/.env");

    /** @var \Illuminate\Foundation\Application $app */
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

$app = call_user_func($createApp);

unset($createApp);

$router = $app->make('router');

collect(glob(__DIR__.'/../routes/testbench-*.php'))
    ->each(function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
