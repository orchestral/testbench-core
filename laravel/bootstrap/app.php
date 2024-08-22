<?php

use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Foundation\Bootstrap\LoadEnvironmentVariablesFromArray;
use Orchestra\Testbench\Foundation\Bootstrap\SyncTestbenchCachedRoutes;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Workbench\Workbench;

use function Orchestra\Testbench\default_environment_variables;

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

    $hasEnvironmentFile = file_exists("{$workingPath}/.env");

    return Application::create(
        $config['laravel'],
        static function ($app) use ($config, $hasEnvironmentFile) {
            Workbench::start($app, $config);
            Workbench::discoverRoutes($app, $config);

            if ($hasEnvironmentFile === false) {
                (new LoadEnvironmentVariablesFromArray(
                    ! empty($config['env']) ? $config['env'] : default_environment_variables()
                ))->bootstrap($app);
            }
        },
        ['load_environment_variables' => $hasEnvironmentFile, 'extra' => $config->getExtraAttributes()],
    );
};

if (! defined('TESTBENCH_WORKING_PATH') && is_string(getenv('TESTBENCH_WORKING_PATH'))) {
    define('TESTBENCH_WORKING_PATH', getenv('TESTBENCH_WORKING_PATH'));
}

$app = $createApp(realpath(__DIR__.'/../'));

unset($createApp);

(new SyncTestbenchCachedRoutes)->bootstrap($app);

return $app;
