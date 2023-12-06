<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Symfony\Component\Yaml\Yaml;

use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\transform_relative_path;

/**
 * @api
 *
 * @phpstan-type TExtraConfig array{
 *   env: array,
 *   providers: array<int, class-string>,
 *   dont-discover: array<int, string>,
 *   bootstrappers: class-string|array<int, class-string>|null
 * }
 * @phpstan-type TOptionalExtraConfig array{
 *   env?: array,
 *   providers?: array<int, class-string>,
 *   dont-discover?: array<int, string>,
 *   bootstrappers?: class-string|array<int, class-string>|null
 * }
 * @phpstan-type TWorkbenchConfig array{
 *   start: string,
 *   user: string|int|null,
 *   guard: string|null,
 *   install: bool,
 *   welcome: bool|null,
 *   sync: array<int, array{from: string, to: string}>,
 *   build: array<int, string>,
 *   assets: array<int, string>,
 *   discovers: TWorkbenchDiscoversConfig
 * }
 * @phpstan-type TOptionalWorkbenchConfig array{
 *   start?: string,
 *   user?: string|int|null,
 *   guard?: string|null,
 *   install?: bool,
 *   welcome?: bool|null,
 *   sync?: array<int, array{from: string, to: string}>,
 *   build?: array<int, string>,
 *   assets?: array<int, string>,
 *   discovers?: TWorkbenchOptionalDiscoversConfig
 * }
 * @phpstan-type TWorkbenchDiscoversConfig array{
 *   config: bool,
 *   web: bool,
 *   api: bool,
 *   commands: bool,
 *   components: bool,
 *   views: bool
 * }
 * @phpstan-type TWorkbenchOptionalDiscoversConfig array{
 *   config?: bool,
 *   web?: bool,
 *   api?: bool,
 *   commands?: bool,
 *   components?: bool,
 *   views?: bool
 * }
 * @phpstan-type TConfig array{
 *   laravel: string|null,
 *   env: array,
 *   providers: array<int, class-string>,
 *   dont-discover: array<int, string>,
 *   migrations: string|array<int, string>|bool,
 *   seeders: class-string|array<int, class-string>|bool,
 *   bootstrappers: class-string|array<int, class-string>|null,
 *   workbench: TOptionalWorkbenchConfig
 * }
 * @phpstan-type TOptionalConfig array{
 *   laravel?: string|null,
 *   env?: array,
 *   providers?: array<int, class-string>,
 *   dont-discover?: array<int, string>,
 *   migrations?: string|array<int, string>|bool,
 *   seeders?: class-string|array<int, class-string>|bool,
 *   bootstrappers?: class-string|array<int, class-string>|null,
 *   workbench?: TOptionalWorkbenchConfig|null
 * }
 */
class Config extends Fluent implements ConfigContract
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array<string, mixed>
     *
     * @phpstan-var TConfig
     */
    protected $attributes = [
        'laravel' => null,
        'env' => [],
        'providers' => [],
        'dont-discover' => [],
        'migrations' => [],
        'seeders' => false,
        'bootstrappers' => [],
        'workbench' => [],
    ];

    /**
     * The Workbench default configuration.
     *
     * @var array<string, mixed>
     *
     * @phpstan-var TWorkbenchConfig
     */
    protected $workbenchConfig = [
        'start' => '/',
        'user' => null,
        'guard' => null,
        'install' => true,
        'welcome' => null,
        'sync' => [],
        'build' => [],
        'assets' => [],
        'discovers' => [
            'config' => false,
            'web' => false,
            'api' => false,
            'commands' => false,
            'components' => false,
            'views' => false,
        ],
    ];

    /**
     * The Workbench discovers default configuration.
     *
     * @var array<string, mixed>
     *
     * @phpstan-var TWorkbenchDiscoversConfig
     */
    protected $workbenchDiscoversConfig = [
        'config' => false,
        'web' => false,
        'api' => false,
        'commands' => false,
        'components' => false,
        'views' => false,
    ];

    /**
     * The cached configuration used during tests.
     *
     * @var static|null
     */
    protected static $cachedConfiguration;

    /**
     * Load configuration from Yaml file.
     *
     * @param  string  $workingPath
     * @param  string|null  $filename
     * @param  array<string, mixed>  $defaults
     * @return static
     */
    public static function loadFromYaml(string $workingPath, ?string $filename = 'testbench.yaml', array $defaults = [])
    {
        $filename = $filename ?? 'testbench.yaml';
        $config = $defaults;

        $filename = LazyCollection::make(static function () use ($filename) {
            yield $filename;
            yield "{$filename}.example";
            yield "{$filename}.dist";
        })->filter(static function ($file) use ($workingPath) {
            return file_exists($workingPath.DIRECTORY_SEPARATOR.$file);
        })->first();

        if (! \is_null($filename)) {
            /**
             * @var array<string, mixed> $config
             *
             * @phpstan-var TOptionalConfig $config
             */
            $config = Yaml::parseFile($workingPath.DIRECTORY_SEPARATOR.$filename);

            $config['laravel'] = transform(Arr::get($config, 'laravel'), static function ($path) use ($workingPath) {
                return transform_relative_path($path, $workingPath);
            });

            if (isset($config['env']) && \is_array($config['env']) && Arr::isAssoc($config['env'])) {
                $config['env'] = parse_environment_variables($config['env']);
            }
        }

        return new static($config);
    }

    /**
     * Load (and cache) configuration from Yaml file.
     *
     * @param  string  $workingPath
     * @param  string|null  $filename
     * @param  array<string, mixed>  $defaults
     * @return static
     *
     * @codeCoverageIgnore
     */
    public static function cacheFromYaml(string $workingPath, ?string $filename = 'testbench.yaml', array $defaults = [])
    {
        if (\is_null(static::$cachedConfiguration)) {
            static::$cachedConfiguration = static::loadFromYaml($workingPath, $filename, $defaults);
        }

        return static::$cachedConfiguration;
    }

    /**
     * Add additional service providers.
     *
     * @param  array<int, class-string<\Illuminate\Support\ServiceProvider>>  $providers
     * @return $this
     */
    public function addProviders(array $providers)
    {
        $this->attributes['providers'] = array_unique(array_merge($this->attributes['providers'], $providers));

        return $this;
    }

    /**
     * Get extra attributes.
     *
     * @return array<string, mixed>
     *
     * @phpstan-return TExtraConfig
     */
    public function getExtraAttributes(): array
    {
        return [
            'env' => Arr::get($this->attributes, 'env', []),
            'bootstrappers' => Arr::get($this->attributes, 'bootstrappers', []),
            'providers' => Arr::get($this->attributes, 'providers', []),
            'dont-discover' => Arr::get($this->attributes, 'dont-discover', []),
        ];
    }

    /**
     * Get workbench attributes.
     *
     * @return array<string, mixed>
     *
     * @phpstan-return TWorkbenchConfig
     */
    public function getWorkbenchAttributes(): array
    {
        $config = array_merge(
            $this->workbenchConfig,
            $this->attributes['workbench'],
        );

        $config['discovers'] = array_merge(
            $this->workbenchDiscoversConfig,
            Arr::get($this->attributes, 'workbench.discovers', [])
        );

        /** @var TWorkbenchConfig $config */
        return $config;
    }

    /**
     * Get workbench discovers attributes.
     *
     * @return array<string, mixed>
     *
     * @phpstan-return TWorkbenchDiscoversConfig
     */
    public function getWorkbenchDiscoversAttributes(): array
    {
        return Arr::get($this->getWorkbenchAttributes(), 'discovers');
    }
}
