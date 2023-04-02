<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use function Orchestra\Testbench\parse_environment_variables;
use function Orchestra\Testbench\transform_relative_path;
use Symfony\Component\Yaml\Yaml;

/**
 * @phpstan-type TConfig array{
 *   laravel: string|null,
 *   env: array,
 *   providers: array,
 *   dont-discover: array,
 *   migrations: array|bool,
 *   bootstrappers: array
 * }
 * @phpstan-type TOptionalConfig array{
 *   laravel?: string|null,
 *   env?: array,
 *   providers?: array,
 *   dont-discover?: array,
 *   migrations?: array|bool,
 *   bootstrappers?: array
 * }
 */
class Config extends Fluent
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var TConfig
     */
    protected $attributes = [
        'laravel' => null,
        'env' => [],
        'providers' => [],
        'dont-discover' => [],
        'migrations' => [],
        'bootstrappers' => [],
    ];

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
        $filename ??= 'testbench.yaml';
        $config = $defaults;

        if (file_exists("{$workingPath}/{$filename}")) {
            /** @var TOptionalConfig $config */
            $config = Yaml::parseFile("{$workingPath}/{$filename}");

            $config['laravel'] = transform(Arr::get($config, 'laravel'), function ($path) use ($workingPath) {
                return transform_relative_path($path, $workingPath);
            });

            if (isset($config['env']) && \is_array($config['env']) && Arr::isAssoc($config['env'])) {
                $config['env'] = parse_environment_variables($config['env']);
            }
        }

        return new static($config);
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
     * @return array{env: array, bootstrappers: array, providers: array, dont-discover: array}
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
}
