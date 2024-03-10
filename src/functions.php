<?php

namespace Orchestra\Testbench;

use Closure;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ProcessUtils;
use Illuminate\Support\Str;
use Illuminate\Testing\PendingCommand;
use InvalidArgumentException;
use Orchestra\Testbench\Foundation\Env;
use PHPUnit\Runner\Version;
use RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Create Laravel application instance.
 *
 * @api
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
 * @api
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase|\Illuminate\Contracts\Foundation\Application  $context
 * @param  string  $command
 * @param  array<string, mixed>  $parameters
 * @return \Illuminate\Testing\PendingCommand|int
 */
function artisan($context, string $command, array $parameters = [])
{
    if ($context instanceof ApplicationContract) {
        return $context->make(ConsoleKernel::class)->call($command, $parameters);
    }

    $command = $context->artisan($command, $parameters);

    return $command instanceof PendingCommand ? $command->run() : $command;
}

/**
 * Run remote action using Testbench CLI.
 *
 * @api
 *
 * @param  string  $command
 * @param  array<string, mixed>  $env
 * @return \Symfony\Component\Process\Process
 */
function remote(string $command, array $env = []): Process
{
    $phpBinary = transform(
        \defined('PHP_BINARY') ? PHP_BINARY : (new PhpExecutableFinder())->find(),
        static function ($phpBinary) {
            return ProcessUtils::escapeArgument((string) $phpBinary);
        }
    );

    $binary = \defined('TESTBENCH_DUSK') ? 'testbench-dusk' : 'testbench';

    $commander = realpath(__DIR__.'/../vendor/autoload.php') !== false
        ? $binary
        : ProcessUtils::escapeArgument((string) package_path("vendor/bin/{$binary}"));

    return Process::fromShellCommandline(
        implode(' ', [$phpBinary, $commander, $command]),
        package_path(),
        array_merge(defined_environment_variables(), $env)
    );
}

/**
 * Run callback only once.
 *
 * @param  mixed  $callback
 * @return \Closure():mixed
 */
function once($callback): Closure
{
    $response = new Foundation\UndefinedValue();

    return function () use ($callback, &$response) {
        if ($response instanceof Foundation\UndefinedValue) {
            $response = value($callback) ?? null;
        }

        return $response;
    };
}

/**
 * Register after resolving callback.
 *
 * @api
 *
 * @param  \Illuminate\Contracts\Foundation\Application  $app
 * @param  string  $name
 * @param  (\Closure(object, \Illuminate\Contracts\Foundation\Application):(mixed))|null  $callback
 * @return void
 */
function after_resolving($app, string $name, ?Closure $callback = null): void
{
    $app->afterResolving($name, $callback);

    if ($app->resolved($name)) {
        value($callback, $app->make($name), $app);
    }
}

/**
 * Load migration paths.
 *
 * @param  \Illuminate\Contracts\Foundation\Application  $app
 * @param  array<int, string>|string  $paths
 * @return void
 */
function load_migration_paths($app, $paths): void
{
    after_resolving($app, 'migrator', static function ($migrator) use ($paths) {
        foreach (Arr::wrap($paths) as $path) {
            /** @var \Illuminate\Database\Migrations\Migrator $migrator */
            $migrator->path($path);
        }
    });
}

/**
 * Get default environment variables.
 *
 * @return array<int, string>
 *
 * @deprecated
 *
 * @codeCoverageIgnore
 */
function default_environment_variables(): array
{
    return parse_environment_variables(
        Collection::make([
            'APP_KEY' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF',
            'APP_DEBUG' => true,
        ])->when(! \defined('TESTBENCH_DUSK'), static function ($variables) {
            return $variables->put('DB_CONNECTION', 'testing');
        })->transform(static function ($value, $key) {
            return $_SERVER[$key] ?? $_ENV[$key] ?? $value;
        })->filter(static function ($value) {
            return ! \is_null($value);
        })
    );
}

/**
 * Get defined environment variables.
 *
 * @api
 *
 * @return array<string, mixed>
 */
function defined_environment_variables(): array
{
    return Collection::make(array_merge($_SERVER, $_ENV))
        ->keys()
        ->mapWithKeys(static function (string $key) {
            return [$key => Env::forward($key)];
        })
        ->put('TESTBENCH_WORKING_PATH', package_path())
        ->all();
}

/**
 * Get default environment variables.
 *
 * @api
 *
 * @param  iterable<string, mixed>  $variables
 * @return array<int, string>
 */
function parse_environment_variables($variables): array
{
    return Collection::make($variables)
        ->transform(static function ($value, $key) {
            if (\is_bool($value) || \in_array($value, ['true', 'false'])) {
                $value = \in_array($value, [true, 'true']) ? '(true)' : '(false)';
            } elseif (\is_null($value) || \in_array($value, ['null'])) {
                $value = '(null)';
            } else {
                $value = $key === 'APP_DEBUG' ? sprintf('(%s)', Str::of($value)->ltrim('(')->rtrim(')')) : "'{$value}'";
            }

            return "{$key}={$value}";
        })->values()->all();
}

/**
 * Refresh router lookups.
 *
 * @api
 *
 * @param  \Illuminate\Routing\Router  $router
 * @return void
 */
function refresh_router_lookups(Router $router): void
{
    $router->getRoutes()->refreshNameLookups();
}

/**
 * Transform relative path.
 *
 * @api
 *
 * @param  string  $path
 * @param  string  $workingPath
 * @return string
 */
function transform_relative_path(string $path, string $workingPath): string
{
    return Str::startsWith($path, './')
        ? rtrim($workingPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.mb_substr($path, 2)
        : $path;
}

/**
 * Get the default skeleton path.
 *
 * @param  string  $path
 * @return string
 */
function default_skeleton_path(string $path = ''): string
{
    $path = $path != '' ? ltrim($path, DIRECTORY_SEPARATOR) : '';

    return rtrim((string) realpath(__DIR__."/../laravel/{$path}"), DIRECTORY_SEPARATOR);
}

/**
 * Get the path to the package folder.
 *
 * @api
 *
 * @param  string  $path
 * @return string
 */
function package_path(string $path = ''): string
{
    $workingPath = \defined('TESTBENCH_WORKING_PATH')
        ? TESTBENCH_WORKING_PATH
        : getcwd();

    if (Str::startsWith($path, './')) {
        return transform_relative_path($path, $workingPath);
    }

    $path = $path != '' ? ltrim($path, DIRECTORY_SEPARATOR) : '';

    return rtrim($workingPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
}

/**
 * Get the workbench configuration.
 *
 * @api
 *
 * @return array<string, mixed>
 */
function workbench(): array
{
    /** @var \Orchestra\Testbench\Contracts\Config $config */
    $config = app()->bound(Contracts\Config::class)
        ? app()->make(Contracts\Config::class)
        : new Foundation\Config();

    return $config->getWorkbenchAttributes();
}

/**
 * Get the path to the workbench folder.
 *
 * @api
 *
 * @param  string  $path
 * @return string
 */
function workbench_path(string $path = ''): string
{
    $path = $path != '' ? ltrim($path, DIRECTORY_SEPARATOR) : '';

    return package_path('workbench'.DIRECTORY_SEPARATOR.$path);
}

/**
 * Get the migration path by type.
 *
 * @api
 *
 * @param  string|null  $type
 * @return string
 *
 * @throws \InvalidArgumentException
 */
function laravel_migration_path(?string $type = null): string
{
    $path = realpath(
        \is_null($type) ? base_path('migrations') : base_path("migrations/{$type}")
    );

    if ($path === false) {
        throw new InvalidArgumentException(sprintf('Unable to resolve migration path for type [%s]', $type ?? 'laravel'));
    }

    return $path;
}

/**
 * Laravel version compare.
 *
 * @api
 *
 * @param  string  $version
 * @param  string|null  $operator
 * @return int|bool
 */
function laravel_version_compare(string $version, ?string $operator = null)
{
    /** @phpstan-ignore-next-line */
    $laravel = Application::VERSION === '8.x-dev' ? '8.0.0' : Application::VERSION;

    if (\is_null($operator)) {
        return version_compare($laravel, $version);
    }

    return version_compare($laravel, $version, $operator);
}

/**
 * PHPUnit version compare.
 *
 * @api
 *
 * @param  string  $version
 * @param  string|null  $operator
 * @return int|bool
 */
function phpunit_version_compare(string $version, ?string $operator = null)
{
    if (! class_exists(Version::class)) {
        throw new RuntimeException('Unable to verify PHPUnit version');
    }

    if (\is_null($operator)) {
        return version_compare(Version::id(), $version);
    }

    return version_compare(Version::id(), $version, $operator);
}
