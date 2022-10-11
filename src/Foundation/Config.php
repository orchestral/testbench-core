<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Symfony\Component\Yaml\Yaml;

class Config extends Fluent
{
    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array{laravel: string|null, env: array, providers: array, dont-discover: array}
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
     * @param  string $workingPath
     * @param  array<string, mixed>  $defaults
     * @param  string|null  $filename
     * @return static
     */
    public static function loadFromYaml(string $workingPath, array $defaults = [], ?string $filename = 'testbench.yaml')
    {
        $filename ??= 'testbench.yaml';
        $config = $defaults;

        if (file_exists("{$workingPath}/{$filename}")) {
            $config = Yaml::parseFile("{$workingPath}/{$filename}");

            $config['laravel'] = transform(Arr::get($config, 'laravel'), function ($basePath) use ($defaultBasePath, $workingPath) {
                return str_replace('./', $workingPath.'/', $basePath);
            });
        }

        return new static($config);
    }

    /**
     * Add additional service providers.
     *
     * @param array<int, class-string<\Illuminate\Support\ServiceProvider>>  $providers
     * @return $this
     */
    public function addProviders(array $providers)
    {
        if (! isset($this->attributes['providers'])) {
            $this->attributes['providers'] = [];
        }

        $this->attributes['providers'] = array_unique($this->attributes['providers'] + $providers);

        return $this;
    }
}
