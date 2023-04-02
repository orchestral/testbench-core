<?php

namespace Orchestra\Testbench;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
 *
 * @deprecated
 */
function default_environment_variables(): array
{
    return parse_environment_variables(
        Collection::make([
            'APP_KEY' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF',
            'APP_DEBUG' => true,
        ])->when(! \defined('TESTBENCH_DUSK'), function ($variables) {
            return $variables->put('DB_CONNECTION', 'testing');
        })->transform(function ($value, $key) {
            return $_SERVER[$key] ?? $_ENV[$key] ?? $value;
        })->filter(function ($value) {
            return ! \is_null($value);
        })
    );
}

/**
 * Get default environment variables.
 *
 * @param  iterable<string, mixed>  $variables
 * @return array<int, string>
 */
function parse_environment_variables($variables): array
{
    return Collection::make($variables)
        ->transform(function ($value, $key) {
            if (\is_null($value) || \in_array($value, ['null'])) {
                $value = '(null)';
            } elseif (\is_bool($value) || \in_array($value, ['true', 'false'])) {
                $value = \in_array($value, [true, 'true']) ? '(true)' : '(false)';
            } else {
                $value = $key === 'APP_DEBUG' ? "({$value})" : "'{$value}'";
            }

            return "{$key}={$value}";
        })->values()->all();
}

function transform_relative_path(string $path, string $workingPath): string
{
    return Str::startsWith($path, './')
        ? str_replace('./', rtrim($workingPath, '/').'/', $path)
        : $path;
}
