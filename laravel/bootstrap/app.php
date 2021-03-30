<?php

use Orchestra\Testbench\Console\Commander;

$APP_KEY = $_SERVER['APP_KEY'] ?? $_ENV['APP_KEY'] ?? 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF';
$DB_CONNECTION = $_SERVER['DB_CONNECTION'] ?? $_ENV['DB_CONNECTION'] ??  'testing';

$config = ['env' => ['APP_KEY="'.$APP_KEY.'"', 'DB_CONNECTION="'.$DB_CONNECTION.'"'], 'providers' => []];

$app = (new Commander($config, getcwd()))->laravel();

unset($APP_KEY, $DB_CONNECTION, $config);

if (file_exists(__DIR__.'/../routes/testbench.php')) {
    $router = $app->make('router');

    require __DIR__.'/../routes/testbench.php';
}

return $app;
