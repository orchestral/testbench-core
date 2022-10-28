<?php

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Config;

$workingPath = realpath(__DIR__.'/../');

$APP_KEY = $_SERVER['APP_KEY'] ?? $_ENV['APP_KEY'] ?? 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF';
$DB_CONNECTION = $_SERVER['DB_CONNECTION'] ?? $_ENV['DB_CONNECTION'] ?? 'testing';

$config = Config::loadFromYaml(
    workingPath: $workingPath,
    defaults: ['env' => ['APP_KEY="'.$APP_KEY.'"', 'DB_CONNECTION="'.$DB_CONNECTION.'"'], 'providers' => []]
);

/** @var \Illuminate\Foundation\Application $app */
$app = Application::create(
    basePath: $config['laravel'],
    options: ['enables_package_discoveries' => true, 'extra' => $config->getExtraAttributes()],
);

unset($APP_KEY, $DB_CONNECTION, $workingPath, $config);

$router = $app->make('router');

collect(glob(__DIR__.'/../routes/testbench-*.php'))
    ->each(function ($routeFile) use ($app, $router) {
        require $routeFile;
    });

return $app;
