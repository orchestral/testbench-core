<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;

/**
 * Create Laravel application instance.
 *
 * @param  string|null  $basePath
 * @param  (callable(\Illuminate\Foundation\Application):void)|null  $resolvingCallback
 * @param  array{extra?: array{providers?: array, dont-discover?: array}, load_environment_variables?: bool, enabled_package_discoveries?: bool}  $options
 * @return \Orchestra\Testbench\Foundation\Application
 */
function container(?string $basePath = null, ?callable $resolvingCallback = null, array $options = [])
{
    return (new Foundation\Application($basePath, $resolvingCallback))->configure($options);
}

/**
 * Run artisan command.
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
 * @param  string  $command
 * @param  array<string, mixed>  $parameters
 * @return \Illuminate\Testing\PendingCommand|int
 */
function artisan(Contracts\TestCase $testbench, string $command, array $parameters = [])
{
    return tap($testbench->artisan($command, $parameters), function ($artisan) {
        if ($artisan instanceof PendingCommand) {
            $artisan->run();
        }
    });
}

/**
 * Get default environment variables.
 *
 * @return array<int, string>
 */
function default_environment_variables(): array
{
    $APP_KEY = $_SERVER['APP_KEY'] ?? $_ENV['APP_KEY'] ?? 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF';
    $DB_CONNECTION = $_SERVER['DB_CONNECTION'] ?? $_ENV['DB_CONNECTION'] ?? 'testing';

    return array_filter([
        'APP_KEY="'.$APP_KEY.'"',
        'APP_DEBUG=(true)',
        ! defined('TESTBENCH_DUSK') ? 'DB_CONNECTION="'.$DB_CONNECTION.'"' : null,
    ]);
}
