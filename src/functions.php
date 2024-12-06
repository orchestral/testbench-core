<?php

namespace Orchestra\Testbench;

use Closure;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ProcessUtils;
use Illuminate\Support\Str;
use Illuminate\Testing\PendingCommand;
use InvalidArgumentException;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\Foundation\Env;
use PHPUnit\Runner\Version;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Create Laravel application instance.
 *
 * @api
 *
 * @param  string|null  $basePath
 * @param  (callable(\Illuminate\Foundation\Application):(void))|null  $resolvingCallback
 * @param  array{extra?: array{providers?: array, dont-discover?: array, env?: array}, load_environment_variables?: bool, enabled_package_discoveries?: bool}  $options
 * @param  \Orchestra\Testbench\Foundation\Config|null  $config
 * @return \Orchestra\Testbench\Foundation\Application
 */
function container(
    ?string $basePath = null,
    ?callable $resolvingCallback = null,
    array $options = [],
    ?Config $config = null
): Foundation\Application {
    if ($config instanceof Config) {
        return Foundation\Application::makeFromConfig($config, $resolvingCallback, $options);
    }

    return Foundation\Application::make($basePath, $resolvingCallback, $options);
}

/**
 * Run artisan command.
 *
 * @api
 *
 * @param  \Orchestra\Testbench\Contracts\TestCase|\Illuminate\Contracts\Foundation\Application  $context
 * @param  string  $command
 * @param  array<string, mixed>  $parameters
 * @return int
 */
function artisan(Contracts\TestCase|ApplicationContract $context, string $command, array $parameters = []): int
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
 * @param  array<int, string>|string  $command
 * @param  array<string, mixed>|string  $env
 * @param  bool|null  $tty
 * @return \Symfony\Component\Process\Process
 */
function remote(array|string $command, array|string $env = [], ?bool $tty = null): Process
{
    $binary = \defined('TESTBENCH_DUSK') ? 'testbench-dusk' : 'testbench';

    $commander = is_file($vendorBin = package_path('vendor', 'bin', $binary))
        ? ProcessUtils::escapeArgument((string) $vendorBin)
        : $binary;

    if (\is_string($env)) {
        $env = ['APP_ENV' => $env];
    }

    Arr::add($env, 'TESTBENCH_PACKAGE_REMOTE', '(true)');

    $process = Process::fromShellCommandline(
        command: Arr::join([php_binary(true), $commander, ...Arr::wrap($command)], ' '),
        cwd: package_path(),
        env: array_merge(defined_environment_variables(), $env)
    );

    if (\is_bool($tty)) {
        $process->setTty($tty);
    }

    return $process;
}

/**
 * Run callback only once.
 *
 * @api
 *
 * @param  mixed  $callback
 * @return \Closure():mixed
 */
function once($callback): Closure
{
    $response = new Foundation\UndefinedValue;

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
function after_resolving(ApplicationContract $app, string $name, ?Closure $callback = null): void
{
    $app->afterResolving($name, $callback);

    if ($app->resolved($name)) {
        value($callback, $app->make($name), $app);
    }
}

/**
 * Load migration paths.
 *
 * @api
 *
 * @param  \Illuminate\Contracts\Foundation\Application  $app
 * @param  array<int, string>|string  $paths
 * @return void
 */
function load_migration_paths(ApplicationContract $app, array|string $paths): void
{
    after_resolving($app, 'migrator', static function ($migrator) use ($paths) {
        foreach (Arr::wrap($paths) as $path) {
            /** @var \Illuminate\Database\Migrations\Migrator $migrator */
            $migrator->path($path);
        }
    });
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
        ->mapWithKeys(static fn (string $key) => [$key => Env::forward($key)])
        ->unless(
            Env::has('TESTBENCH_WORKING_PATH'), static fn ($env) => $env->put('TESTBENCH_WORKING_PATH', package_path())
        )->all();
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
                $value = $key === 'APP_DEBUG' ? \sprintf('(%s)', Str::of($value)->ltrim('(')->rtrim(')')) : "'{$value}'";
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
    return str_starts_with($path, './')
        ? rtrim($workingPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.mb_substr($path, 2)
        : $path;
}

/**
 * Get the default skeleton path.
 *
 * @api
 *
 * @param  array|string  $path
 * @return string
 */
function default_skeleton_path(array|string $path = ''): string
{
    return (string) realpath(join_paths(__DIR__, '..', 'laravel', ...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path)));
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
function default_migration_path(?string $type = null): string
{
    $path = realpath(
        \is_null($type) ? base_path('migrations') : base_path(join_paths('migrations', $type))
    );

    if ($path === false) {
        throw new InvalidArgumentException(\sprintf('Unable to resolve migration path for type [%s]', $type ?? 'laravel'));
    }

    return $path;
}

/**
 * Get the path to the package folder.
 *
 * @api
 *
 * @param  array|string  $path
 * @return string
 */
function package_path(array|string $path = ''): string
{
    $argumentCount = \func_num_args();

    $workingPath = \defined('TESTBENCH_WORKING_PATH')
        ? TESTBENCH_WORKING_PATH
        : Env::get('TESTBENCH_WORKING_PATH', getcwd());

    if ($argumentCount === 1 && \is_string($path) && str_starts_with($path, './')) {
        return transform_relative_path($path, $workingPath);
    }

    $path = join_paths(...Arr::wrap($argumentCount > 1 ? \func_get_args() : $path));

    return str_starts_with($path, './')
        ? transform_relative_path($path, $workingPath)
        : join_paths(rtrim($workingPath, DIRECTORY_SEPARATOR), $path);
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
        : new Foundation\Config;

    return $config->getWorkbenchAttributes();
}

/**
 * Get the path to the workbench folder.
 *
 * @api
 *
 * @param  array|string  $path
 * @return string
 */
function workbench_path(array|string $path = ''): string
{
    return package_path('workbench', ...Arr::wrap(\func_num_args() > 1 ? \func_get_args() : $path));
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
 *
 * @deprecated
 */
#[\Deprecated(message: 'Use `Orchestra\Testbench\default_migration_path()` instead', since: '9.5.1')]
function laravel_migration_path(?string $type = null): string
{
    return default_migration_path($type);
}

/**
 * Determine if vendor symlink exists on the laravel application.
 *
 * @api
 *
 * @param  \Illuminate\Contracts\Foundation\Application  $app
 * @param  string|null  $workingPath
 * @return bool
 */
function laravel_vendor_exists(ApplicationContract $app, ?string $workingPath = null): bool
{
    $filesystem = new Filesystem;

    $appVendorPath = $app->basePath('vendor');
    $workingPath ??= package_path('vendor');

    return $filesystem->isFile(join_paths($appVendorPath, 'autoload.php')) &&
        $filesystem->hash(join_paths($appVendorPath, 'autoload.php')) === $filesystem->hash(join_paths($workingPath, 'autoload.php'));
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
function laravel_version_compare(string $version, ?string $operator = null): int|bool
{
    /** @var string $laravel */
    $laravel = transform(
        Application::VERSION,
        fn (string $version) => $version === '11.x-dev' ? '11.0.0' : $version, // @phpstan-ignore identical.alwaysFalse
    );

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
 *
 * @throws \RuntimeException
 */
function phpunit_version_compare(string $version, ?string $operator = null): int|bool
{
    if (! class_exists(Version::class)) {
        throw new RuntimeException('Unable to verify PHPUnit version');
    }

    /** @var string $phpunit */
    $phpunit = transform(
        Version::id(),
        fn (string $version) => str_starts_with($version, '11.5-') ? '11.5.0' : $version,
    );

    if (\is_null($operator)) {
        return version_compare($phpunit, $version);
    }

    return version_compare($phpunit, $version, $operator);
}

/**
 * Determine the PHP Binary.
 *
 * @api
 *
 * @param  bool  $escape
 * @return string
 */
function php_binary(bool $escape = false): string
{
    $phpBinary = (new Support\PhpExecutableFinder)->find(false) ?: 'php';

    return $escape === true ? ProcessUtils::escapeArgument((string) $phpBinary) : $phpBinary;
}

/**
 * Join the given paths together.
 *
 * @param  string|null  $basePath
 * @param  string  ...$paths
 * @return string
 */
function join_paths(?string $basePath, string ...$paths): string
{
    foreach ($paths as $index => $path) {
        if (empty($path) && $path !== '0') {
            unset($paths[$index]);
        } else {
            $paths[$index] = DIRECTORY_SEPARATOR.ltrim($path, DIRECTORY_SEPARATOR);
        }
    }

    return $basePath.implode('', $paths);
}

/**
 * Ensure the provided `$app` return an instance of Laravel application or throw an exception.
 *
 * @internal
 *
 * @param  \Illuminate\Foundation\Application|null  $app
 * @param  string|null  $caller
 * @return \Illuminate\Foundation\Application
 *
 * @throws \Orchestra\Testbench\Exceptions\ApplicationNotAvailableException
 */
function laravel_or_fail($app, ?string $caller = null): Application
{
    if ($app instanceof Application) {
        return $app;
    }

    if (\is_null($caller)) {
        $caller = transform(debug_backtrace()[1], function ($debug) {
            /** @phpstan-ignore isset.offset */
            if (isset($debug['class']) && isset($debug['function'])) {
                return \sprintf('%s::%s', $debug['class'], $debug['function']);
            }

            return $debug['function'];
        });
    }

    throw Exceptions\ApplicationNotAvailableException::make($caller);
}
