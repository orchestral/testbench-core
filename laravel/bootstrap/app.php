<?php

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Bootstrap\SyncTestbenchCachedRoutes;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Workbench;

use function Orchestra\Testbench\join_paths;

/**
 * Create Laravel application.
 *
 * @param  string  $workingPath
 * @return \Illuminate\Foundation\Application
 */
$createApp = static function (string $workingPath) {
    $config = Config::loadFromYaml(
        workingPath: defined('TESTBENCH_WORKING_PATH') ? TESTBENCH_WORKING_PATH : $workingPath,
        filename: defined('TESTBENCH_WORKING_PATH') ? 'testbench.yaml' : join_paths($workingPath, 'bootstrap', 'cache', 'testbench.yaml')
    );

    $hasEnvironmentFile = ! is_null($config['laravel'])
        ? is_file(join_paths($config['laravel'], '.env'))
        : is_file(join_paths($workingPath, '.env'));

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

$app = $createApp(realpath(join_paths(__DIR__, '..')));

unset($createApp);

(new SyncTestbenchCachedRoutes)->bootstrap($app);

return $app;
