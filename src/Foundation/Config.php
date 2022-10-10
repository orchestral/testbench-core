<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Symfony\Component\Yaml\Yaml;

class Config extends Fluent
{
    public static function loadFromYaml(string $workingPath, ?string $filename = 'testbench.yaml')
    {
        $config = [
            'env' => ['APP_KEY="AckfSECXIvnK5r28GVIWUAxmbBSjTsmF"', 'DB_CONNECTION="testing"'],
            'providers' => [],
            'dont-discover' => [],
        ];

        if (file_exists("{$workingPath}/{$filename}")) {
            $config = Yaml::parseFile("{$workingPath}/{$filename}");

            $config['laravel'] = transform(Arr::get($config, 'laravel'), function ($basePath) use ($defaultBasePath, $workingPath) {
                return str_replace('./', $workingPath.'/', $basePath);
            });
        }

        return new static($config);
    }
}
