<?php

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Console\Bootstrap\SyncTestbenchCachedRoutes;
use Orchestra\Testbench\Workbench\Workbench;

/**
 * Create Laravel application.
 *
 * @param  string  $workingPath
 * @return \Illuminate\Foundation\Application
 */
$createApp = static function (string $workingPath) {
    $config = Config::loadFromYaml(
        defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : $workingPath
    );

    $hasEnvironmentFile = ! is_null($config['laravel'])
        ? file_exists($config['laravel'].'/.env')
        : file_exists("{$workingPath}/.env");

    return Application::create(
        basePath: $config['laravel'],
        options: ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
        resolvingCallback: static function ($app) use ($config) {
            Workbench::startWithProviders($app, $config);
            Workbench::discoverRoutes($app, $config);
        },
    );
};

if (! defined('TESTBENCH_WORKING_PATH') && is_string(getenv('TESTBENCH_WORKING_PATH'))) {
    define('TESTBENCH_WORKING_PATH', getenv('TESTBENCH_WORKING_PATH'));
}

$app = $createApp(realpath(__DIR__.'/../'));

unset($createApp);

(new SyncTestbenchCachedRoutes)->bootstrap($app);

return $app;
