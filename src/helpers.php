<?php

namespace Orchestra\Testbench;

use Illuminate\Testing\PendingCommand;

/**
 * Create Laravel application instance.
 *
 * @param  string|null  $basePath
 * @param  (callable(\Illuminate\Foundation\Application):(void))|null  $resolvingCallback
 * @param  array{extra?: array{providers?: array, dont-discover?: array, env?: array}, load_environment_variables?: bool, enabled_package_discoveries?: bool}  $options
 * @return \Orchestra\Testbench\Foundation\Application
 */
function container(?string $basePath = null, ?callable $resolvingCallback = null, array $options = []): Foundation\Application
{
    return (new Foundation\Application($basePath, $resolvingCallback))->configure($options);
}

/**
 * Run artisan command.
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase  $testbench
 * @param  string  $command
 * @param  array<string, mixed>  $parameters
 * @return int
 */
function artisan(Contracts\TestCase $testbench, string $command, array $parameters = []): int
{
    $command = $testbench->artisan($command, $parameters);

    return $command instanceof PendingCommand ? $command->run() : $command;
}

/**
 * Get default environment variables.
 *
 * @return array<int, string|null>
 */
function default_environment_variables(): array
{
    return collect(['APP_KEY' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF', 'APP_DEBUG' => 'true', 'DB_CONNECTION' => null])
        ->transform(fn ($value, $key) => ($_SERVER[$key] ?? $_ENV[$key] ?? $value))
        ->filter(fn ($value) => ! \is_null($value))
        ->transform(function ($value, $key) {
            $value = $key === 'APP_DEBUG' ? "({$value})" : "'{$value}'";

            return "{$key}={$value}";
        })->values()->all();
}
