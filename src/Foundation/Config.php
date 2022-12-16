<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Symfony\Component\Yaml\Yaml;

/**
 * @phpstan-type TConfig array{laravel: string|null, env: array, providers: array, dont-discover: array}
 * @phpstan-type TOptionalConfig array{laravel?: string|null, env?: array, providers?: array, dont-discover?: array}
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

            $config['laravel'] = transform(Arr::get($config, 'laravel'), function ($basePath) use ($workingPath) {
                return str_replace('./', $workingPath.'/', $basePath);
            });
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
        $this->attributes['providers'] = array_unique($this->attributes['providers'] + $providers);

        return $this;
    }

    /**
     * Get extra attributes.
     *
     * @return array{providers: array, dont-discover: array}
     */
    public function getExtraAttributes(): array
    {
        /** @var array{providers: array, dont-discover: array} $extra */
        $extra = Arr::only($this->attributes, ['providers', 'dont-discover', 'env']);

        return $extra;
    }
}
